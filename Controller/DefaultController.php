<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 06.07.2015 15:43
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Controller;


use SciGroup\TinymcePluploadFileManagerBundle\Entity\ContentFile;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends ContainerAware
{
    public function uploadAction(Request $request, $mappingType)
    {
        $response = [
            'status' => 0,
            'message' => ''
        ];

        try {
            $em = $this->container->get('doctrine.orm.default_entity_manager');
            $pathResolver = $this->container->get('sci_group.tpfm.mapping_resolver')->resolve($mappingType);

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

                $em->persist($contentFile);
            }

            if (count($fileNames) > 0) {
                $em->flush();
            }

            $response['files'] = $fileNames;
        } catch (\Exception $e) {
            $response['status'] = 1;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}