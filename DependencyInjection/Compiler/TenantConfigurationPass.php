<?php

namespace Funkymed\TenantAwareBundle\DependencyInjection\Compiler;

use Doctrine\ORM\EntityManager;
use Funkymed\TenantAwareBundle\DependencyInjection\TenantAwareExtension;
use Funkymed\TenantAwareBundle\Entity\Tenant;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;

class TenantConfigurationPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function __construct(private TenantAwareExtension $extension)
    {
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.dbal.default_connection')) {
            return;
        }

        $hostname = $container->getParameter('kernel.hostname');
        if (!$hostname) {
            return;
        }

        // load configuration processors
        $config =  $this->extension->getConfig();

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $tenant = $entityManager->getRepository(Tenant::class)->findOneBy(['hostname' => $hostname]);

        if ($tenant) {
            foreach ($config['processors'] as $processor) {
                if (class_exists($processor)) {
                    $p = new $processor($tenant, $container);
                    $p->process();
                }

            }
        } else {
            throw new \InvalidArgumentException(sprintf('Tenant with hostname "%s" not found', $hostname));
        }
    }

    private function debug($container, $hostname)
    {
        dump($container->get('doctrine.orm.entity_manager')->getConnection()->getParams());
        dump($hostname);
    }

}
