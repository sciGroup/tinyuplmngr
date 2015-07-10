<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 17:14
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Model;


abstract class AbstractContentFileManager
{
    abstract public function add(ContentFile $contentFile);

    abstract public function remove(ContentFile $contentFile);

    abstract public function findFilesByMappingType($mappingType);
}