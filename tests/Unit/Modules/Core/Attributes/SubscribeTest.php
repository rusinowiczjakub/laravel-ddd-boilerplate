<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Attributes;

use Modules\Core\Attributes\Subscribe;

test('object', function () {
    $subscribe = new Subscribe('test');

    expect($subscribe->target)->toBe('test');
});
