<?php

namespace App\Repositories;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class ProviderRepository
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Create a new service repository instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register the application service providers.
     *
     * @return void
     */
    public function load(array $providers): void
    {
        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    /**
     * Register a service provider.
     *
     * @param  string $provider
     * @return void
     */
    public function register(string $provider): void
    {
        if (!class_exists($provider)) {
            throw new \RuntimeException("Provider class {$provider} not found");
        }

        $provider = new $provider($this->app);

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        if (method_exists($provider, 'boot')) {
            $provider->boot();
        }
    }

    /**
     * Get the registered service providers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProviders(): Collection
    {
        return collect($this->app['config']['app.providers']);
    }

    /**
     * Get the deferred service providers.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDeferredProviders(): Collection
    {
        return collect($this->app['config']['app.deferred_providers']);
    }

    /**
     * Get the application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication(): Application
    {
        return $this->app;
    }
}
