<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Transaction;

interface TransactionManager
{
    public function run(callable $callback, int $attempts = 1): mixed;

    public function afterCommit(callable $callback): void;

    public function withinTransaction(): bool;
}
