<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Core\Attributes;

use Modules\Core\Attributes\CommandHandler;

test('object', function () {
    $subscribe = new CommandHandler('test');

    expect($subscribe->target)->toBe('test');
});
