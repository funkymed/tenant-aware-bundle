<?php

namespace Funkymed\TenantAwareBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class TenantAwareExtension extends Extension
{
    private array $config;

    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $this->compileConfig($configs);
    }

    private function compileConfig(array $configs): void
    {
        $configuration = new Configuration(); // This is our bundle's configuration class.
        $this->config ??= $this->processConfiguration($configuration, $configs);
    }

    /**
     * Retrieves the configuration for the bundle.
     */
    public function getConfig(): array
    {
        return $this->config ?? [];
    }
}
