<?php

declare(strict_types=1);

namespace Tests\Stub;

use Modules\Core\Attributes\Subscribe;

#[Subscribe(target: TestEventStub::class)]
class TestListenerStub
{
    public function __invoke(TestEventStub $event): void
    {
        // do nothing
    }
}
