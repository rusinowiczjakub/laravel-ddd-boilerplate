<?php

declare(strict_types=1);

namespace Tests\Stub;

use Illuminate\Contracts\Bus\Dispatcher;

class DispatcherStub implements Dispatcher
{
    public function dispatch($command)
    {
        // TODO: Implement dispatch() method.
    }

    public function dispatchSync($command, $handler = null)
    {
        // TODO: Implement dispatchSync() method.
    }

    public function dispatchNow($command, $handler = null)
    {
        // TODO: Implement dispatchNow() method.
    }

    public function hasCommandHandler($command)
    {
        // TODO: Implement hasCommandHandler() method.
    }

    public function getCommandHandler($command)
    {
        // TODO: Implement getCommandHandler() method.
    }

    public function pipeThrough(array $pipes)
    {
        // TODO: Implement pipeThrough() method.
    }

    public function map(array $map): array
    {
        return $map;
    }
}
