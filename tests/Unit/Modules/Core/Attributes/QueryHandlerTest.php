<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Attributes;

use Modules\Core\Attributes\QueryHandler;

test('object', function () {
    $subscribe = new QueryHandler('test');

    expect($subscribe->target)->toBe('test');
});
