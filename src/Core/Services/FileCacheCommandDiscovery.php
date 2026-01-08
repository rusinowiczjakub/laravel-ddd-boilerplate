<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Contracts\Bus\Dispatcher;
use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Command\Contracts\CommandDiscovery;

class FileCacheCommandDiscovery extends AttributeClassDiscovery implements CommandDiscovery
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
            CommandHandler::class
        );
    }

    public function boot(?callable $registerCallback = null): void
    {
        $commandsPath = storage_path(config('commands.cache_path'));

        if (! file_exists($commandsPath)) {
            $this->discover();
        }

        if (file_exists($commandsPath)) {
            $commandMap = include $commandsPath;

            $this->dispatcher->map($commandMap);
        }
    }
}
