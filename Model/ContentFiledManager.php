<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 03.08.2015 17:06
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Model;


use Doctrine\Common\Annotations\AnnotationReader;
use SciGroup\TinymcePluploadFileManagerBundle\Validator\Constraints\ContentFiled;
use SciGroup\TinymcePluploadFileManagerBundle\Validator\Constraints\ContentFile as ContentFileAnnotation;

class ContentFiledManager
{
    /**
     * @var AbstractContentFileManager
     */
    private $contentFileManager;

    public function __construct(AbstractContentFileManager $contentFileManager)
    {
        $this->contentFileManager = $contentFileManager;
    }

    public function getContentFiles($object)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reader = new AnnotationReader();
        $annotations = $reader->getClassAnnotations($reflectedClass);

        $isContentFiledClass = false;
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ContentFiled) {
                $isContentFiledClass = true;
            }
        }

        if (!$isContentFiledClass) {
            throw new \InvalidArgumentException('Only @ContentFiled annotated classes are supported!');
        }

        $contentFiles = [];
        foreach ($reflectedClass->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof ContentFileAnnotation) {
                    $mappingType = $object->{$annotation->mappingTypeMethod}();

                    $contentFiles = array_merge($contentFiles, $this->contentFileManager->findFilesByMappingType($mappingType));
                }
            }
        }

        return $contentFiles;
    }
}