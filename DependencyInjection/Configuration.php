<?php

namespace Funkymed\TenantAwareBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tenant_aware');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('processors')
                    ->prototype('scalar')->end()
                    ->defaultValue([])
                    ->info('A list of all the class processor configuration of your tenant')
                ->end() 
            ->end()
        ;

        return $treeBuilder;
    }
}