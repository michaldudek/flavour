<?php
namespace MD\Flavour\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * MD Flavour Symfony Container extension.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
class FlavourExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @param array            $configs   Configs.
     * @param ContainerBuilder $container Container builder.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ .'/../Resources/config'));
        $loader->load('services.yml');
    }
}
