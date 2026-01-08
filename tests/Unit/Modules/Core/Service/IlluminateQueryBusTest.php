<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Service;

use Illuminate\Contracts\Bus\Dispatcher;
use Mockery;
use Modules\Core\Services\IlluminateQueryBus;
use Tests\Stub\TestQueryStub;

it('dispatches query', function () {
    $dispatcher = Mockery::mock(Dispatcher::class);

    $query = new TestQueryStub;

    $queryBus = new IlluminateQueryBus(
        $dispatcher
    );

    $dispatcher
        ->expects()
        ->dispatch($query)
        ->once();

    $queryBus->dispatch($query);
});

afterAll(function () {
    Mockery::close();
});
