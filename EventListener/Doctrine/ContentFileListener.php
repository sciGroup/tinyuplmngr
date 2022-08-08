<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 16:06
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\EventListener\Doctrine;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SciGroup\TinymcePluploadFileManagerBundle\Doctrine\ContentFileManager;
use SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFile;
use SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFiledInterface;
use SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFiledManager;
use SciGroup\TinymcePluploadFileManagerBundle\Resolver\MappingResolver;

class ContentFileListener implements EventSubscriber
{
    private MappingResolver $mappingResolver;

    public function __construct(MappingResolver $mappingResolver)
    {
        $this->mappingResolver = $mappingResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove',
        ];
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        if ($object instanceof ContentFile) {
            $pathResolver = $this->mappingResolver->resolve($object->getMappingType());
            $file = $pathResolver->getDirectory(true).'/'.$object->getFileName();
            if (file_exists($file)) {
                unlink($file);
            }
        } elseif ($object instanceof ContentFiledInterface) {
            $contentFileManager = new ContentFileManager($event->getEntityManager(), 0);
            $filedManager = new ContentFiledManager($contentFileManager);

            $contentFiles = $filedManager->getContentFiles($object);
            foreach ($contentFiles as $contentFile) {
                $event->getEntityManager()->remove($contentFile);
            }
        }
    }
}