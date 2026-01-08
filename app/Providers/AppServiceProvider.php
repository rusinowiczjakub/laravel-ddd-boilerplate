<?php

namespace App\Providers;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher as EventDispatcher;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Pennant\Feature;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\Core\Command\Contracts\CommandDiscovery;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Core\Events\Contracts\EventDiscovery;
use Modules\Core\Query\Contracts\QueryDiscovery;
use Modules\Core\Services\FileCacheCommandDiscovery;
use Modules\Core\Services\FileCacheEventDiscovery;
use Modules\Core\Services\FileCacheQueryDiscovery;
use Modules\Core\Services\IlluminateCommandBus;
use Modules\Core\Services\IlluminateEventBus;
use Modules\Core\Services\IlluminateQueryBus;
use Modules\Shared\Domain\Events\EventMappingService;
use Modules\Shared\Domain\Repositories\EventStoreRepository;
use Modules\Shared\Domain\Transaction\TransactionManager;
use Modules\Shared\Infrastructure\Repository\MongoEventStoreRepository;
use Modules\Shared\Infrastructure\Transaction\IlluminateTransactionManager;
use Modules\Workspaces\Infrastructure\Models\WorkspaceModel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EventDiscovery::class, fn ($app) => new FileCacheEventDiscovery(
            config('events.discovery_path'),
            config('events.cache_path'),
            config('events.should_discover')
        ));

        $this->app->singleton(QueryDiscovery::class, fn ($app) => new FileCacheQueryDiscovery(
            config('queries.discovery_path'),
            config('queries.cache_path'),
            config('queries.should_discover'),
            $app->make(Dispatcher::class)
        ));

        $this->app->singleton(CommandDiscovery::class, fn ($app) => new FileCacheCommandDiscovery(
            config('commands.discovery_path'),
            config('commands.cache_path'),
            config('commands.should_discover'),
            $app->make(Dispatcher::class)
        ));

        $this->app->singleton(EventBus::class, fn (Application $app) => new IlluminateEventBus(
            $app->make(EventDispatcher::class),
            new EventMappingService(

            )
        ));
        $this->app->singleton(CommandBus::class, IlluminateCommandBus::class);
        $this->app->singleton(QueryBus::class, IlluminateQueryBus::class);
        $this->app->singleton(EventStoreRepository::class, MongoEventStoreRepository::class);
        $this->app->singleton(TransactionManager::class, IlluminateTransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $this->app->make(EventDiscovery::class)->boot();
        $this->app->make(CommandDiscovery::class)->boot();
        $this->app->make(QueryDiscovery::class)->boot();

        Cashier::useCustomerModel(WorkspaceModel::class);

        // Define feature flags
        $this->defineFeatures();
    }

    /**
     * Define Pennant feature flags.
     */
    private function defineFeatures(): void
    {
    }
}
