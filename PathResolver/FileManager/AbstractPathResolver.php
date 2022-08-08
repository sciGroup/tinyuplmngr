<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 11:18
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\PathResolver\FileManager;


use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractPathResolver
{
    protected ?array $mapping;
    protected ?string $webDirectory;

    abstract public function getDirectory($absolute = false);

    public function setMapping(array $mapping): void
    {
        $this->mapping = $mapping;
    }

    public function setWebDirectory(string $dir): void
    {
        $this->webDirectory = $dir;
    }

    public function generateFileName(UploadedFile $uploadedFile): string
    {
        $mappingUrl = [];
        foreach ($this->mapping['args'] as $key => $value) {
            $mappingUrl[] = $key.'='.$value;
        }

        $hash = sha1(sha1($this->mapping['path_resolver'].'?'.implode('&', $mappingUrl)).sha1(uniqid(mt_rand(), true)));
        $extension = strtolower($uploadedFile->getClientOriginalExtension());

        return $hash.($extension !== '' ? '.'.$extension : '');
    }
}