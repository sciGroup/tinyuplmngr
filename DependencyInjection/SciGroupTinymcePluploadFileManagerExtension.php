<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class SciGroupTinymcePluploadFileManagerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('sci_group_tinymce_plupload_file_manager.mappings', $config['mappings']);
        $container->setParameter('sci_group_tinymce_plupload_file_manager.garbage_file_ttl', $config['garbage_file_ttl']);

        $yamlFileLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $yamlFileLoader->load(sprintf('%s.yaml', $config['db_driver']));
        $yamlFileLoader->load('services.yaml');
    }
}
