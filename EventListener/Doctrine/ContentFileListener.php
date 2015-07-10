<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 16:06
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\EventListener\Doctrine;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFile;
use SciGroup\TinymcePluploadFileManagerBundle\Resolver\MappingResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentFileListener implements EventSubscriber
{
    /**
     * @var MappingResolver
     */
    private $mappingResolver;

    public function __construct(MappingResolver $mappingResolver)
    {
        $this->mappingResolver = $mappingResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'preRemove'
        );
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
        }
    }
}