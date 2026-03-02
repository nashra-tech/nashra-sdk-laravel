<?php

namespace Nashra\Sdk\Tests;

use Nashra\Sdk\NashraServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            NashraServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Nashra' => \Nashra\Sdk\Facades\Nashra::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('nashra.api_key', 'test-api-key');
        $app['config']->set('nashra.base_url', 'https://app.nashra.ai/api/v1');
        $app['config']->set('nashra.timeout', 30);
        $app['config']->set('nashra.max_retries', 0);
    }
}
