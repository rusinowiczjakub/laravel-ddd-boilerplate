<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Transaction;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Domain\Transaction\TransactionManager;
use Throwable;

final class IlluminateTransactionManager implements TransactionManager
{
    /**
     * @throws Throwable
     */
    public function run(callable $callback, int $attempts = 1): mixed
    {
        return DB::transaction($callback, $attempts);
    }

    public function afterCommit(callable $callback): void
    {
        DB::afterCommit($callback);
    }

    public function withinTransaction(): bool
    {
        return DB::transactionLevel() > 0;
    }
}
