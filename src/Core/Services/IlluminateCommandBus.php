<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Contracts\Bus\Dispatcher;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Command\Contracts\Command;

class IlluminateCommandBus implements CommandBus
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {
    }

    public function dispatch(Command $command): mixed
    {
        return $this->dispatcher->dispatch($command);
    }
}
