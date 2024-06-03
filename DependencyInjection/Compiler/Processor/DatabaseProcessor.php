<?php

namespace Funkymed\TenantAwareBundle\DependencyInjection\Compiler\Processor;

// use this as an exemple to create your own replacement configuration
class DatabaseProcessor extends ProcessorAbstract
{
    public function process()
    {
        // get current definition
        $definition = $this->container->getDefinition('doctrine.dbal.default_connection');
        $configuration = $definition->getArguments();

        // update it from the tenant information
        $configuration[0]["host"] = $this->tenant->getDatabaseHost();
        $configuration[0]["dbname"] = $this->tenant->getDatabaseName();
        $configuration[0]["user"] = $this->tenant->getDatabaseUser();
        $configuration[0]["password"] = $this->tenant->getDatabasePassword();

        // replace the current configuration everything is in the cache now
        $definition->replaceArgument(0, $configuration[0]);
    }
}
