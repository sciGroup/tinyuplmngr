<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 11:18
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\PathResolver\FileManager;


abstract class AbstractPathResolver
{
    /**
     * @var array
     */
    protected $mapping;

    /**
     * @var string
     */
    protected $webDirectory;

    abstract public function getDirectory($absolute = false);

    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function setWebDirectory($dir)
    {
        $this->webDirectory = $dir;
    }

    public function generateFileName($uploadedFile)
    {
        $mappingUrl = [];
        foreach ($this->mapping['args'] as $key => $value) {
            $mappingUrl[] = $key.'='.$value;
        }

        $hash = sha1(sha1($this->mapping['path_resolver'].'?'.implode('&', $mappingUrl)).sha1(uniqid(mt_rand(), true)));
        $extension = strtolower($uploadedFile->getClientOriginalExtension());

        return $hash.(strlen($extension) > 0 ? '.'.$extension : '');
    }
}