<?php

namespace Nashra\Sdk;

use Illuminate\Support\ServiceProvider;

class NashraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/nashra.php', 'nashra');

        $this->app->singleton(Nashra::class, function ($app): Nashra {
            $config = $app['config']['nashra'];

            return new Nashra(
                apiKey: $config['api_key'] ?? '',
                baseUrl: $config['base_url'] ?? 'https://app.nashra.ai/api/v1',
                timeout: $config['timeout'] ?? 30,
                maxRetries: $config['max_retries'] ?? 3,
            );
        });

        $this->app->alias(Nashra::class, 'nashra');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/nashra.php' => config_path('nashra.php'),
            ], 'nashra-config');
        }
    }
}
