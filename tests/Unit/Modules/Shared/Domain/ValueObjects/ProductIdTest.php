<?php

declare(strict_types=1);

use Modules\Shared\Domain\ValueObjects\ProductId;

it('creates a product id', function () {
    $productId = ProductId::create();

    expect($productId)->toBeInstanceOf(ProductId::class);
    expect($productId->value())->toBeString();
    expect($productId->value())->not->toBeEmpty();
});

it('creates different product ids', function () {
    $productId1 = ProductId::create();
    $productId2 = ProductId::create();

    expect($productId1->value())->not->toBe($productId2->value());
});

it('creates product id from existing value', function () {
    $value = '123e4567-e89b-12d3-a456-426614174000';
    $productId = new ProductId($value);

    expect($productId->value())->toBe($value);
});
