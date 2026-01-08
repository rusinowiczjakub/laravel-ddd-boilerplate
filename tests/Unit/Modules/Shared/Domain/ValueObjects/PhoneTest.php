<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidPhoneException;
use Modules\Shared\Domain\ValueObjects\Phone;

test('creates a valid phone number with correct normalization', function (string $input, string $expected) {
    $phone = new Phone($input);

    expect($phone->value())->toBe($expected);
})->with([
    ['+48 123 456 789', '+48123456789'],
    ['+1-202-555-0182', '+12025550182'],
    ['+4915112345678', '+4915112345678'],
]);

test('throws exception for phone number without country code', function () {
    new Phone('123456789');
})->throws(InvalidPhoneException::class);

test('throws exception for invalid phone format', function (string $invalidPhone) {
    new Phone($invalidPhone);
})->with([
    '+00 123 456 789',
    '+1234', // za krótki numer
    '+abcd1234567', // litery
])->throws(InvalidPhoneException::class);
