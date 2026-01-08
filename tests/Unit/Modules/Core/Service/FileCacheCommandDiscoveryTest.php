<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Service;

use Illuminate\Contracts\Bus\Dispatcher;
use Mockery;
use Modules\Core\Services\FileCacheCommandDiscovery;
use Tests\Stub\TestCommandHandlerStub;
use Tests\Stub\TestCommandStub;

const COMMAND_CACHE_PATH = 'framework/cache/command_discovery_test.php';

it('discover commands and creates cache file', function () {
    $dispatcher = Mockery::mock(Dispatcher::class);

    $discovery = new FileCacheCommandDiscovery(
        'tests/Stub/',
        COMMAND_CACHE_PATH,
        true,
        $dispatcher
    );

    $discovery->discover();

    expect(file_exists(storage_path(COMMAND_CACHE_PATH)))->toBeTrue();

    $cachedCommands = include storage_path(COMMAND_CACHE_PATH);

    expect($cachedCommands)->toBe([TestCommandStub::class => TestCommandHandlerStub::class]);
});

afterAll(function () {
    if (file_exists(storage_path(COMMAND_CACHE_PATH))) {
        unlink(storage_path(COMMAND_CACHE_PATH));
    }

    Mockery::close();
});
