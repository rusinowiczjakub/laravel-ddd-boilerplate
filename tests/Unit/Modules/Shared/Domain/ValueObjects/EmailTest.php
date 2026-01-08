<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidEmailException;
use Modules\Shared\Domain\ValueObjects\Email;

it('creates a valid email', function () {
    $email = new Email('user@example.com');

    expect($email->value())->toBe('user@example.com');
});

it('throws exception for invalid email', function (string $invalidEmail) {
    new Email($invalidEmail);
})->with([
    'plainaddress',
    '@missingusername.com',
    'username@.com',
])->throws(InvalidEmailException::class);
