<?php

namespace SciGroup\TinymcePluploadFileManagerBundle\Model;

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 */

abstract class ContentFile
{
    protected $id;

    /**
     * @var string
     */
    protected $mappingType;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var boolean
     */
    protected $isSubmitted;

    /**
     * @var \DateTime
     */
    protected $uploadedAt;

    public function __construct()
    {
        $this->isSubmitted = false;
        $this->uploadedAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMappingType()
    {
        return $this->mappingType;
    }

    /**
     * @param string $mappingType
     */
    public function setMappingType($mappingType)
    {
        $this->mappingType = $mappingType;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return boolean
     */
    public function getIsSubmitted()
    {
        return $this->isSubmitted;
    }

    /**
     * @param boolean $isSubmitted
     */
    public function setIsSubmitted($isSubmitted)
    {
        $this->isSubmitted = $isSubmitted;
    }

    /**
     * @return \DateTime
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    /**
     * @param \DateTime $uploadedAt
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;
    }
}