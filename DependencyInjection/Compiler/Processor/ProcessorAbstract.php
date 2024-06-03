<?php

namespace Funkymed\TenantAwareBundle\DependencyInjection\Compiler\Processor;

use Doctrine\ORM\EntityManager;
use Funkymed\TenantAwareBundle\Entity\Tenant;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class ProcessorAbstract implements ProcessorInterface
{
    protected Tenant $tenant;

    protected ContainerBuilder $container;

    public function __construct(
        Tenant $tenant,
        ContainerBuilder $container
    ) {
        $this->tenant = $tenant;
        $this->container = $container;
    }

    public function process()
    {

    }

}
