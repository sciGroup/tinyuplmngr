<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load(sprintf('%s.xml', $config['db_driver']));
        $loader->load('services.xml');
    }
}
