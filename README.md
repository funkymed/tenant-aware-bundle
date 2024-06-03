# Tenant-aware-bundle

Tenant Aware Bundle will help you to manage multiple configuration of your app.

The configuration come from database.

## Installation

```bash
composer require funkymed/tenant-aware-bundle
```

create your a configuration `config/packages/tenant_aware.yaml`

```yaml
tenant_aware:
    processors:
        - Funkymed\TenantAwareBundle\DependencyInjection\Compiler\Processor\DummyProcessor
        - Funkymed\TenantAwareBundle\DependencyInjection\Compiler\Processor\DatabaseProcessor
```

Modify you Kernel.php like this to use a cache by tenant

```php
<?php

// src/Kernel.php

namespace App;

use Funkymed\TenantAwareBundle\TenantAwareKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    private ?string $hostname;

    public function __construct(
        string $environment,
        bool $debug,
        string $hostname
    ) {
        parent::__construct($environment, $debug);
        $this->hostname = $hostname;
    }

    public function getCacheDir(): string
    {
        if ($this->getHostname()) {
            return $this->getProjectDir().'/var/cache/'.$this->environment.'/'.$this->getHostname();
        }
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        if ($this->getHostname()) {
            return $this->getProjectDir().'/var/log/'.$this->getHostname();
        }
        return $this->getProjectDir().'/var/log';
    }

    public function getName()
    {
        return str_replace('-', '_', $this->getHostname());
    }

    public function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.hostname'] = $this->getHostname();

        return $parameters;
    }
    public function getHostname()
    {
        $hostname = $this->getHost();
        return $hostname ? $hostname : $this->hostname;

    }
    public function getHost()
    {
        $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
        $sourceTransformations = array(
            "HTTP_X_FORWARDED_HOST" => function ($value) {
                $elements = explode(',', $value);
                return trim(end($elements));
            }
        );
        $host = '';
        foreach ($possibleHostSources as $source) {
            if (!empty($host)) {
                break;
            }
            if (empty($_SERVER[$source])) {
                continue;
            }
            $host = $_SERVER[$source];
            if (array_key_exists($source, $sourceTransformations)) {
                $host = $sourceTransformations[$source]($host);
            }
        }

        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

        return trim($host);
    }
}
```

and then replace `bin/console` with this code to make it compatible

```php
#!/usr/bin/env php
<?php

// bin/console

use App\Kernel;
use Funkymed\TenantAwareBundle\Command\TenantAwareApplication;
use Symfony\Component\Console\Input\ArgvInput;

if (!is_dir(dirname(__DIR__).'/vendor')) {
    throw new LogicException('Dependencies are missing. Try running "composer install".');
}

if (!is_file(dirname(__DIR__).'/vendor/autoload_runtime.php')) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $input = new ArgvInput();
    $env = $input->getParameterOption(['--env', '-e'], getenv('APP_ENV') ?: 'dev');
    $debug = getenv('APP_DEBUG') !== '0' && $env !== 'prod';

    // Added support for tenant
    $hostname = $input->getParameterOption('--tenant');

    $kernel = new Kernel($env, $debug, $hostname);
    $kernel->boot();

    return new TenantAwareApplication($kernel);

};
```

Now you can use the default commands of symfony but with a tenant configuration

```bash
bin/console d:d:c --tenant=localhost
```

The param tenant is the hostname you want to get the configuration

### Configuration

Added configuration of your database in `.env` and `config/packages/doctrine.yaml`

```.env
# .env
DATABASE_HOST="localhost"
DATABASE_USER="root"
DATABASE_PASSWORD=""
DATABASE_NAME="tenant"
```

```yaml
doctrine:
    dbal:
        driver: 'pdo_mysql'
        server_version: '8'
        use_savepoints: true
        host: '%env(resolve:DATABASE_HOST)%'
        port: 3306
        user: '%env(resolve:DATABASE_USER)%'
        password: '%env(resolve:DATABASE_URL)%'
        dbname: '%env(resolve:DATABASE_PASSWORD)%'
```

### Create tenant database

```bash
bin/console d:d:c --if-not-exist
bin/console d:s:u -f
```

Add content in your database to manager different hostname (see Tenant Entity)

## Add your Processor

### Add more service

You can process other services than the database
You could want to change an AWS S3 per hostame, or Redis, and or RabbitMQ

Just copy `TenantAwareBundle/DependencyInjection/Compiler/Processor/DummyProcessor.php`

And put in your `DependencyInjection/Compiler/Processor` namespace

Add in your processor what you want to repace

```php
<?php

// src/DependencyInjection/DependencyInjection/Compiler/Processor/DummyProcessor.php

namespace App\DependencyInjection\Compiler\Processor;

// use this as an exemple to create your own replacement configuration
class MyProcessor extends ProcessorAbstract
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
```

Update the configuration

```yaml
tenant_aware:
    processors:
        - App\DependencyInjection\Compiler\Processor\MyProcessor
```

You can add all the processors you want.

You also can replace the Entity Tenant to put the fields you need to manage your tenants.