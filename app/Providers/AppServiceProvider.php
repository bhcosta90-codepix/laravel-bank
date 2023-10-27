<?php

namespace App\Providers;

use App\Services\Contracts\AMQPInterface;
use App\Services\Contracts\RabbitMQInterface;
use App\Services\RabbitMQService;
use Bank\Domain\Integration\PixKeyIntegration;
use BRCas\CA\Contracts\Event\EventManagerInterface;
use CodePix\Bank\Integration\PixKeyIntegrationInterface;
use Illuminate\Support\ServiceProvider;
use Bank\Application\EventManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(PixKeyIntegrationInterface::class, PixKeyIntegration::class);
        $this->app->singleton(EventManagerInterface::class, EventManager::class);
        $this->app->singleton(AMQPInterface::class, RabbitMQService::class);
        $this->app->singleton(RabbitMQInterface::class, RabbitMQService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
