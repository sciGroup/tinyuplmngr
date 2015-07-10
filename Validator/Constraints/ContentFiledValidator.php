<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 17:20
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use SciGroup\TinymcePluploadFileManagerBundle\Resolver\MappingResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Mapping\PropertyMetadata;

class ContentFiledValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var MappingResolver
     */
    private $mappingResolver;

    public function __construct(EntityManager $entityManager, MappingResolver $mappingResolver)
    {
        $this->entityManager = $entityManager;
        $this->mappingResolver = $mappingResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $flushNeeded = false;

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

                    $mappingType = $value->$method();
                    $files = $this->entityManager->getRepository('SciGroupTinymcePluploadFileManagerBundle:ContentFile')->findBy([
                        'mappingType' => $mappingType
                    ]);

                    $accessor = PropertyAccess::createPropertyAccessor();
                    $content = $accessor->getValue($value, $propertyMetadata->getPropertyName());

                    // extract all images from content property
                    preg_match('/src=[\'\"]*(.+?)[\'\"]/', $content, $matches);
                    array_shift($matches);

                    foreach ($matches as $imgFile) {
                        $imgFile = preg_replace('/\?.*$/', '', $imgFile);
                        $imgFile = pathinfo($imgFile, PATHINFO_BASENAME);
                        foreach ($files as $key => $file) {
                            /* @var \SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFile $file */
                            if ($file->getFileName() == $imgFile) {
                                $file->setIsSubmitted(true);
                                unset($files[$key]);
                                $flushNeeded = true;

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

                    foreach ($files as $file) {
                        $this->entityManager->remove($file);
                        $flushNeeded = true;
                    }
                }
            }
        }

        if ($flushNeeded) {
            $this->entityManager->flush();
        }
    }
}