<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Contracts\Bus\Dispatcher;
use Modules\Core\Attributes\QueryHandler;
use Modules\Core\Query\Contracts\QueryDiscovery;

class FileCacheQueryDiscovery extends AttributeClassDiscovery implements QueryDiscovery
{
    public function __construct(
        string $discoveryPath,
        string $cachePath,
        bool $shouldDiscover,
        private readonly Dispatcher $dispatcher
    ) {
        parent::__construct(
            $discoveryPath,
            $cachePath,
            $shouldDiscover,
            QueryHandler::class
        );
    }

    public function boot(?callable $registerCallback = null): void
    {
        $queries = storage_path(config('queries.cache_path'));

        if (! file_exists($queries)) {
            $this->discover();
        }

        if (file_exists($queries)) {
            $queriesMap = include $queries;

            $this->dispatcher->map($queriesMap);
        }
    }
}
