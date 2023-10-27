<?php

namespace App\Providers;

use Bank\Domain\Repositories\AccountRepository;
use Bank\Domain\Repositories\TransactionRepository;
use CodePix\Bank\Application\Repository\AccountRepositoryInterface;
use CodePix\Bank\Application\Repository\PixKeyRepositoryInterface;
use CodePix\Bank\Application\Repository\TransactionRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Bank\Domain\Repositories\PixKeyRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AccountRepositoryInterface::class, AccountRepository::class);
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
