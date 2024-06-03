<?php

namespace Funkymed\TenantAwareBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class TenantAwareApplication extends Application
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $inputDefinition->addOption(new InputOption('--tenant', null, InputOption::VALUE_REQUIRED, 'The hostname of the Tenant', 'localhost'));
        return $inputDefinition;
    }

}
