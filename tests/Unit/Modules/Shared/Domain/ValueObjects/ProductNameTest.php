<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidNameException;
use Modules\Shared\Domain\ValueObjects\ProductName;

it('creates a valid product name', function () {
    $productName = new ProductName('Beautiful Sunset Poster');

    expect($productName->value())->toBe('Beautiful Sunset Poster');
});

it('accepts product names with special characters', function () {
    $productName = new ProductName('Art & Design - 2024');

    expect($productName->value())->toBe('Art & Design - 2024');
});

it('accepts product names with numbers', function () {
    $productName = new ProductName('Poster 123');

    expect($productName->value())->toBe('Poster 123');
});

it('throws exception for empty product name', function () {
    new ProductName('');
})->throws(InvalidNameException::class);

it('throws exception for product name that is too short', function () {
    new ProductName('A');
})->throws(InvalidNameException::class);
