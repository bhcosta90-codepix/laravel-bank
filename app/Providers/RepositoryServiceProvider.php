<?php

namespace App\Providers;

use CodePix\System\Application\Repository\PixKeyRepositoryInterface;
use CodePix\System\Application\Repository\TransactionRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use System\Domain\Repositories\PixKeyRepository;
use System\Domain\Repositories\TransactionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PixKeyRepositoryInterface::class, PixKeyRepository::class);
        $this->app->singleton(TransactionRepositoryInterface::class, TransactionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
