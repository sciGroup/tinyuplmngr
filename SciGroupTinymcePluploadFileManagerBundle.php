<?php

namespace SciGroup\TinymcePluploadFileManagerBundle;

use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 */
class SciGroupTinymcePluploadFileManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterPartsPass());
    }
}