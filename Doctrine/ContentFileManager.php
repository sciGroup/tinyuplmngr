<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 17:14
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Doctrine;


use Doctrine\ORM\EntityManager;
use SciGroup\TinymcePluploadFileManagerBundle\Model\AbstractContentFileManager;
use SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFile;

class ContentFileManager extends AbstractContentFileManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(ContentFile $contentFile)
    {
        $this->entityManager->persist($contentFile);
        $this->entityManager->flush();
    }

    public function remove(ContentFile $contentFile)
    {
        $this->entityManager->remove($contentFile);
        $this->entityManager->flush();
    }

    public function findFilesByMappingType($mappingType)
    {
        return $this->entityManager->getRepository('SciGroupTinymcePluploadFileManagerBundle:ContentFile')->findBy(
            [
                'mappingType' => $mappingType
            ]
        );
    }
}