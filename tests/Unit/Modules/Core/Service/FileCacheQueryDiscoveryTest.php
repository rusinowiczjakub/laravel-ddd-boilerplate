<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Service;

use Illuminate\Contracts\Bus\Dispatcher;
use Mockery;
use Modules\Core\Services\FileCacheQueryDiscovery;
use Tests\Stub\TestQueryHandlerStub;
use Tests\Stub\TestQueryStub;

const QUERY_CACHE_PATH = 'framework/cache/query_discovery_test.php';

it('discover queries and creates cache file', function () {
    $dispatcher = Mockery::mock(Dispatcher::class);

    $discovery = new FileCacheQueryDiscovery(
        'tests/Stub/',
        QUERY_CACHE_PATH,
        true,
        $dispatcher
    );

    $discovery->discover();

    expect(file_exists(storage_path(QUERY_CACHE_PATH)))->toBeTrue();

    $cachedQueries = include storage_path(QUERY_CACHE_PATH);

    expect($cachedQueries)->toBe([TestQueryStub::class => TestQueryHandlerStub::class]);
});

afterAll(function () {
    if (file_exists(storage_path(QUERY_CACHE_PATH))) {
        unlink(storage_path(QUERY_CACHE_PATH));
    }

    Mockery::close();
});
