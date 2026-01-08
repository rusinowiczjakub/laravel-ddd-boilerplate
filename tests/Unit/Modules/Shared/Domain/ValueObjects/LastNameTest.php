<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidNameException;
use Modules\Shared\Domain\ValueObjects\LastName;

it('creates a valid last name', function () {
    $lastName = new LastName('Kowalski');

    expect($lastName->value())->toBe('Kowalski');
});

it('throws exception for invalid last name length', function (string $invalidLastName) {
    expect(fn () => new LastName($invalidLastName))
        ->toThrow(InvalidNameException::class);
})->with([
    '',
    'K',
    str_repeat('B', 56),
]);
