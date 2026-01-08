<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidNameException;
use Modules\Shared\Domain\ValueObjects\FirstName;

it('creates a valid first name', function () {
    $firstName = new FirstName('Jan');

    expect($firstName->value())->toBe('Jan');
});

it('throws exception for invalid first name length', function (string $invalidFirstName) {
    expect(fn () => new FirstName($invalidFirstName))
        ->toThrow(InvalidNameException::class);
})->with([
    '',
    'J',
    str_repeat('A', 56),
]);
