<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Service;

use Illuminate\Contracts\Bus\Dispatcher;
use Mockery;
use Modules\Core\Services\IlluminateCommandBus;
use Tests\Stub\TestCommandStub;

it('dispatches command', function () {
    $dispatcher = Mockery::mock(Dispatcher::class);

    $command = new TestCommandStub;

    $commandBus = new IlluminateCommandBus(
        $dispatcher
    );

    $dispatcher
        ->expects()
        ->dispatch($command)
        ->once();

    $commandBus->dispatch($command);
});

afterAll(function () {
    Mockery::close();
});
