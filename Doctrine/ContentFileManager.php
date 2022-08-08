<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 17:14
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use SciGroup\TinymcePluploadFileManagerBundle\Model\AbstractContentFileManager;
use SciGroup\TinymcePluploadFileManagerBundle\Model\ContentFile;
use SciGroup\TinymcePluploadFileManagerBundle\Entity\ContentFile as EntityContentFile;

class ContentFileManager extends AbstractContentFileManager
{
    private EntityManagerInterface $entityManager;
    private int $garbageFileTtl;

    public function __construct(EntityManagerInterface $entityManager, $garbageFileTtl)
    {
        $this->entityManager = $entityManager;
        $this->garbageFileTtl = (int)$garbageFileTtl;
    }

    public function add(ContentFile $contentFile): void
    {
        $this->entityManager->persist($contentFile);
        $this->entityManager->flush();
    }

    public function remove(ContentFile $contentFile): void
    {
        $this->entityManager->remove($contentFile);
        $this->entityManager->flush();
    }

    public function findFilesByMappingType($mappingType): array
    {
        return $this->entityManager->getRepository(EntityContentFile::class)->findBy(
            [
                'mappingType' => $mappingType
            ]
        );
    }

    public function removeGarbageFiles(): void
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