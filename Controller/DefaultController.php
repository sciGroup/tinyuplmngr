<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 06.07.2015 15:43
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Controller;


use SciGroup\TinymcePluploadFileManagerBundle\PathResolver\FileManager\AbstractPathResolver;
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
            $mapping = $this->container->get('sci_group.tpfm.mapping_resolver')->resolve($mappingType);

            $pathResolver = $this->container->get($mapping['path_resolver']);
            /* @var AbstractPathResolver $pathResolver */
            $pathResolver->setMapping($mapping);
            $pathResolver->setWebDirectory($this->container->getParameter('kernel.root_dir').'/../web');

            $fileNames = [];
            foreach ($request->files as $uploadedFile) {
                /* @var UploadedFile $uploadedFile */
                $dstDirectory = $pathResolver->getDirectory(true);
                $fileName = $pathResolver->generateFileName($uploadedFile);

                $uploadedFile->move($dstDirectory, $fileName);

                $fileNames[] = $pathResolver->getDirectory().'/'.$fileName;
            }

            $response['files'] = $fileNames;
        } catch (\Exception $e) {
            $response['status'] = 1;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}