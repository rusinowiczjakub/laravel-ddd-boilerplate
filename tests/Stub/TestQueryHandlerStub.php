<?php

declare(strict_types=1);

namespace Tests\Stub;

use Modules\Core\Attributes\QueryHandler;

#[QueryHandler(target: TestQueryStub::class)]
class TestQueryHandlerStub
{
    public function __invoke(TestQueryStub $testQuery): void
    {
        // TODO: Implement __invoke() method.
    }
}
