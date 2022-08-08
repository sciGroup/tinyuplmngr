<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('sci_group_tinymce_plupload_file_manager');
        $rootNode = $treeBuilder->getRootNode();

        $supportedDrivers = array('orm');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                    ->defaultValue('orm')
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('garbage_file_ttl')
                    ->defaultValue(3600)
                ->end()
            ->end();
        $this->addMappingSection($rootNode);

        return $treeBuilder;
    }

    private function addMappingSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path_resolver')->isRequired()->end()
                            ->scalarNode('base_path')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}