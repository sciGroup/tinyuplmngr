<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 17:39
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContentFile extends Constraint
{
    /**
     * Mapping name
     *
     * @var string
     */
    public $mapping;

    public $mappingTypeMethod;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return [
            'mapping',
            'mappingTypeMethod'
        ];
    }
}