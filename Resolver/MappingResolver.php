<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 09.07.2015 10:17
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Resolver;


use SciGroup\TinymcePluploadFileManagerBundle\PathResolver\FileManager\AbstractPathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MappingResolver
{
    private ContainerInterface $container;
    private array $mappings;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mappings = $container->getParameter('sci_group_tinymce_plupload_file_manager.mappings') ?? [];
    }

    /**
     * Resolve mapping name
     *
     * @param $name
     * @return AbstractPathResolver
     */
    public function resolve($name)
    {
        foreach ($this->mappings as $mappingName => $mappingValue) {
            $mappingNameParts = explode('_', $mappingName);
            $nameParts = explode('_', $name);
            if ($nameParts[0] === $mappingNameParts[0]) {
                array_shift($mappingNameParts);
                array_shift($nameParts);

                $args = array_combine($mappingNameParts, $nameParts);

                $mapping = array_merge($mappingValue, [
                    'args' => $args
                ]);

                $pathResolver = $this->container->get($mapping['path_resolver']);
                /* @var AbstractPathResolver $pathResolver */
                $pathResolver->setMapping($mapping);

                return $pathResolver;
            }
        }

        throw new \InvalidArgumentException(sprintf('Mapping type %s is not defined!', $name));
    }
}