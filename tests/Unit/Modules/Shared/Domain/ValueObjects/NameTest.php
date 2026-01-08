<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidNameException;
use Modules\Shared\Domain\ValueObjects\Name;

it('creates a valid name', function () {
    $name = new Name('Jan Kowalski');

    expect($name->value())->toBe('Jan Kowalski');
});

it('checks equality of names', function () {
    $nameA = new Name('Jan Kowalski');
    $nameB = new Name('Jan Kowalski');
    $nameC = new Name('Anna Kowalska');

    expect($nameA->equals($nameB))->toBeTrue()
        ->and($nameA->equals($nameC))->toBeFalse();
});

it('throws exception when name is too short or too long', function (string $invalidName) {
    expect(fn () => new Name($invalidName))
        ->toThrow(InvalidNameException::class);
})->with([
    '',
    'A',
    str_repeat('A', 56),
]);
