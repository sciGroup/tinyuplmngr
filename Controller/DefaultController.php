<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 06.07.2015 15:43
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Controller;


use Symfony\Component\DependencyInjection\ContainerAware;
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
            $mappingConfig = $this->container->get('sci_group.tpfm.mapping_resolver')->resolve($mappingType);

        } catch (\Exception $e) {
            $response['status'] = 1;
            $response['message'] = $e->getMessage();
        }

        return new JsonResponse($response);
    }
}