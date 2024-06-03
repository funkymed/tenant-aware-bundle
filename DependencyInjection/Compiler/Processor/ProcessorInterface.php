<?php

namespace Funkymed\TenantAwareBundle\DependencyInjection\Compiler\Processor;

interface ProcessorInterface
{
    /**
     * @return mixed
     */
    public function process();
}
