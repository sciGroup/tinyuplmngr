<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 10:17
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Resolver;


class MappingResolver
{
    /**
     * @var array
     */
    private $mappings;

    public function __construct(array $mappings)
    {
        $this->mappings = $mappings;
    }

    /**
     * Resolve mapping name
     *
     * @param $name
     * @return array
     */
    public function resolve($name)
    {
        foreach ($this->mappings as $mappingName => $mappingValue) {
            $mappingNameParts = explode('_', $mappingName);
            $nameParts = explode('_', $name);
            if ($nameParts[0] == $mappingNameParts[0]) {
                array_shift($mappingNameParts);
                array_shift($nameParts);

                $args = array_combine($mappingNameParts, $nameParts);

                return array_merge($mappingValue, [
                    'args' => $args
                ]);
            }
        }

        throw new \InvalidArgumentException(sprintf('Mapping type %s is not defined!', $mappingType));
    }
}