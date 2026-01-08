<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidDateFormatException;
use Modules\Shared\Domain\ValueObjects\Date;

it('create actual date', function () {
    $dateString = '2025-03-25 12:34:56';
    $date = new Date($dateString);

    expect($date)->toBeInstanceOf(Date::class)
        ->and($date->toString())->toBe($dateString);
});

it('create date from string', function () {
    $dateString = '2025-03-25 12:34:56';
    $date = Date::fromString($dateString);

    expect($date)->toBeInstanceOf(Date::class)
        ->and($date->toString())->toBe($dateString);
});

it('throws exception on invalid date for from string', function () {
    $invalidDateString = 'invalid-date';
    Date::fromString($invalidDateString);
})->throws(InvalidDateFormatException::class);

it('throws exception on invalid date for constructor', function () {
    new Date('invalid-time');
})->throws(InvalidDateFormatException::class);
