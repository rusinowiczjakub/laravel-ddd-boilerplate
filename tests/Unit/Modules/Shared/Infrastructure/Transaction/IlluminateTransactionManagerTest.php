<?php

declare(strict_types=1);

use Modules\Shared\Infrastructure\Transaction\IlluminateTransactionManager;

it('runs callback within transaction', function () {
    $manager = new IlluminateTransactionManager;
    $executed = false;

    $result = $manager->run(function () use (&$executed) {
        $executed = true;

        return 'success';
    });

    expect($executed)->toBeTrue();
    expect($result)->toBe('success');
});

it('returns callback result', function () {
    $manager = new IlluminateTransactionManager;

    $result = $manager->run(function () {
        return 'transaction result';
    });

    expect($result)->toBe('transaction result');
});

it('propagates exceptions from callback', function () {
    $manager = new IlluminateTransactionManager;

    $manager->run(function () {
        throw new \Exception('Test exception');
    });
})->throws(\Exception::class, 'Test exception');

it('supports custom retry attempts', function () {
    $manager = new IlluminateTransactionManager;
    $attempts = 0;

    $result = $manager->run(function () use (&$attempts) {
        $attempts++;

        return 'done';
    }, 5);

    expect($attempts)->toBe(1);
    expect($result)->toBe('done');
});

it('executes after commit callback', function () {
    $manager = new IlluminateTransactionManager;
    $callbackExecuted = false;

    $manager->run(function () use ($manager, &$callbackExecuted) {
        $manager->afterCommit(function () use (&$callbackExecuted) {
            $callbackExecuted = true;
        });
    });

    expect($callbackExecuted)->toBeTrue();
});

it('does not execute after commit callback when exception thrown', function () {
    $manager = new IlluminateTransactionManager;
    $callbackExecuted = false;

    try {
        $manager->run(function () use ($manager, &$callbackExecuted) {
            $manager->afterCommit(function () use (&$callbackExecuted) {
                $callbackExecuted = true;
            });

            throw new \Exception('Rollback');
        });
    } catch (\Exception $e) {
        // Expected exception
    }

    expect($callbackExecuted)->toBeFalse();
});

it('detects when within transaction', function () {
    $manager = new IlluminateTransactionManager;

    $withinTransaction = false;

    $manager->run(function () use ($manager, &$withinTransaction) {
        $withinTransaction = $manager->withinTransaction();
    });

    expect($withinTransaction)->toBeTrue();
});

it('handles nested transactions correctly', function () {
    $manager = new IlluminateTransactionManager;
    $innerTransactionLevel = 0;
    $outerTransactionLevel = 0;

    $manager->run(function () use ($manager, &$outerTransactionLevel, &$innerTransactionLevel) {
        $outerTransactionLevel = $manager->withinTransaction() ? 1 : 0;

        $manager->run(function () use ($manager, &$innerTransactionLevel) {
            $innerTransactionLevel = $manager->withinTransaction() ? 1 : 0;
        });
    });

    expect($outerTransactionLevel)->toBe(1);
    expect($innerTransactionLevel)->toBe(1);
});
