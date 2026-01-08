<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Service;

use Modules\Core\Services\FileCacheEventDiscovery;
use Tests\Stub\TestEventStub;
use Tests\Stub\TestListenerStub;

const CACHE_PATH = 'framework/cache/event_discovery_test.php';

it('discover events and creates cache file', function () {
    $discovery = new FileCacheEventDiscovery(
        'tests/Stub/',
        CACHE_PATH,
        true,
    );

    $discovery->discover();

    expect(file_exists(storage_path(CACHE_PATH)))->toBeTrue();

    $cachedEvents = include storage_path(CACHE_PATH);

    expect($cachedEvents)->toBe([TestEventStub::class => [TestListenerStub::class]]);
});

afterAll(function () {
    if (file_exists(storage_path(CACHE_PATH))) {
        unlink(storage_path(CACHE_PATH));
    }
});
