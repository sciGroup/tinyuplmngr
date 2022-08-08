<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 06.07.2015 15:43
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Controller;


use SciGroup\TinymcePluploadFileManagerBundle\Doctrine\ContentFileManager;
use SciGroup\TinymcePluploadFileManagerBundle\Entity\ContentFile;
use SciGroup\TinymcePluploadFileManagerBundle\Resolver\MappingResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function uploadAction(Request $request, $mappingType, MappingResolver $mappingResolver, ContentFileManager $contentFileManager)
    {
        $response = [
            'status' => 0,
            'message' => ''
        ];

        try {
            $pathResolver = $mappingResolver->resolve($mappingType);

            $fileNames = [];
            foreach ($request->files as $uploadedFile) {
                /* @var UploadedFile $uploadedFile */
                $dstDirectory = $pathResolver->getDirectory(true);
                $fileName = $pathResolver->generateFileName($uploadedFile);

                $uploadedFile->move($dstDirectory, $fileName);

                $fileNames[] = $pathResolver->getDirectory().'/'.$fileName;

                $contentFile = new ContentFile();
                $contentFile->setMappingType($mappingType);
                $contentFile->setFileName($fileName);

                $contentFileManager->add($contentFile);
            }

            $response['files'] = $fileNames;
        } catch (\Exception $e) {
            $response['status'] = 1;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}
