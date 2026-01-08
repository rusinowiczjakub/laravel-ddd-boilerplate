<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidUuidException;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Ramsey\Uuid\Uuid as RamseyUuid;

it('creates a valid Uuid from a valid uuid string', function () {
    $validUuid = RamseyUuid::uuid4()->toString();
    $uuid = new Uuid($validUuid);
    expect($uuid->value())->toBe($validUuid);
});

it('throws an exception when constructed with an invalid uuid string', function () {
    new Uuid('invalid-uuid');
})->throws(InvalidUuidException::class);

it('creates a Uuid using the static create method', function () {
    $uuid = Uuid::create();
    expect($uuid)->toBeInstanceOf(Uuid::class)
        ->and(RamseyUuid::isValid($uuid->value()))->toBeTrue();
});

it('compares two Uuids correctly', function () {
    $uuidString = RamseyUuid::uuid4()->toString();
    $uuid1 = new Uuid($uuidString);
    $uuid2 = new Uuid($uuidString);
    $uuid3 = Uuid::create();

    expect($uuid1->equals($uuid2))->toBeTrue()
        ->and($uuid1->equals($uuid3))->toBeFalse();
});
