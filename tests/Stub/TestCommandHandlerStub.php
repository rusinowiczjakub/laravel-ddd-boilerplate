<?php

declare(strict_types=1);

namespace Tests\Stub;

use Modules\Core\Attributes\CommandHandler;

#[CommandHandler(target: TestCommandStub::class)]
class TestCommandHandlerStub
{
    public function __invoke(TestCommandStub $command): void {}
}
