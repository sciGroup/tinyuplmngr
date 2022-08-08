<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 17:20
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Validator\Constraints;

use SciGroup\TinymcePluploadFileManagerBundle\Doctrine\ContentFileManager;
use SciGroup\TinymcePluploadFileManagerBundle\Resolver\MappingResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Mapping\PropertyMetadata;

class ContentFiledValidator extends ConstraintValidator
{
    private ContentFileManager $contentFileManager;
    private MappingResolver $mappingResolver;

    public function __construct(ContentFileManager $contentFileManager, MappingResolver $mappingResolver)
    {
        $this->contentFileManager = $contentFileManager;
        $this->mappingResolver = $mappingResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        foreach ($this->context->getMetadata()->properties as $propertyMetadata) {
            /* @var PropertyMetadata $propertyMetadata */
            foreach ($propertyMetadata->getConstraints() as $constraint) {
                if ($constraint instanceof ContentFile) {
                    $method = 'get'.Container::camelize($constraint->mapping).'MappingType';
                    if (!method_exists($value, $method)) {
                        throw new \RuntimeException(
                            sprintf('Method "%s" should be defined in class %s', $method, get_class($value))
                        );
                    }

                    $accessor = PropertyAccess::createPropertyAccessor();
                    $propertyName = $propertyMetadata->getPropertyName();
                    $content = $accessor->getValue($value, $propertyName);

                    // check also translations
                    if ($translations = $accessor->getValue($value, 'translations')) {
                        foreach ($translations as $translation) {
                            if ($translation->getField() == $propertyName) {
                                $content .= $translation->getContent();
                            }
                        }
                    }

                    $content = trim($content);
                    if (mb_strlen($content) == 0) {
                        return;
                    }

                    $mappingType = $value->$method();
                    $files = $this->contentFileManager->findFilesByMappingType($mappingType);

                    // extract all images from content property
                    preg_match_all('/src=[\'\"]*(.+?)[\'\"]/', $content, $matches);
                    array_shift($matches);
                    if (count($matches[0]) > 0) {
                        $matches = $matches[0];

                        foreach ($matches as $imgFile) {
                            $imgFile = preg_replace('/\?.*$/', '', $imgFile);
                            $imgFile = pathinfo($imgFile, PATHINFO_BASENAME);
                            foreach ($files as $key => $file) {
                                /* @var \SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFile $file */
                                if ($file->getFileName() == $imgFile) {
                                    $file->setIsSubmitted(true);
                                    unset($files[$key]);

                                    continue 2;
                                }
                            }

                            // remove file
                            $pathResolver = $this->mappingResolver->resolve($mappingType);

                            $imgFile = $pathResolver->getDirectory(true).'/'.$imgFile;
                            if (file_exists($imgFile)) {
                                unlink($imgFile);
                            }
                        }
                    }

                    foreach ($files as $file) {
                        $this->contentFileManager->remove($file);
                    }
                }
            }
        }
    }
}