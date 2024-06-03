<?php

namespace Funkymed\TenantAwareBundle;

use Funkymed\TenantAwareBundle\DependencyInjection\TenantAwareExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Funkymed\TenantAwareBundle\DependencyInjection\Compiler\TenantConfigurationPass;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class TenantAwareBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $extension = $this->getContainerExtension();
        $container->addCompilerPass(
            new TenantConfigurationPass($extension),
            PassConfig::TYPE_AFTER_REMOVING
        );
    }
}
