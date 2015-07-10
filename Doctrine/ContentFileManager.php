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

    /**
     * @var integer
     */
    private $garbageFileTtl;

    public function __construct(EntityManager $entityManager, $garbageFileTtl)
    {
        $this->entityManager = $entityManager;
        $this->garbageFileTtl = $garbageFileTtl;
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

    public function removeGarbageFiles()
    {
        $dql = 'SELECT cf FROM SciGroupTinymcePluploadFileManagerBundle:ContentFile cf WHERE cf.uploadedAt < :uploaded_at';

        $threshold = new \DateTime(sprintf('now - %d secs', $this->garbageFileTtl));

        $garbageFiles = $this->entityManager->createQuery($dql)->setParameter('uploaded_at', $threshold)->iterate();
        foreach ($garbageFiles as $garbageFile) {
            $this->entityManager->remove($garbageFile[0]);
        }

        $this->entityManager->flush();
    }
}