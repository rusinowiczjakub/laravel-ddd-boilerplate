<?php

declare(strict_types=1);

use Modules\Shared\Domain\Exceptions\InvalidUuidException;
use Modules\Shared\Domain\ValueObjects\Id;
use Ramsey\Uuid\Uuid as RamseyUuid;

it('creates a valid Id from a valid uuid string', function () {
    $validUuid = RamseyUuid::uuid4()->toString();
    $id = new Id($validUuid);
    expect($id->value())->toBe($validUuid);
});

it('throws an exception when constructed with an invalid uuid string', function () {
    new Id('invalid-uuid');
})->throws(InvalidUuidException::class);

it('creates a Id using the static create method', function () {
    $id = Id::create();
    expect($id)->toBeInstanceOf(Id::class)
        ->and(RamseyUuid::isValid($id->value()))->toBeTrue();
});

it('compares two Ids correctly', function () {
    $uuidString = RamseyUuid::uuid4()->toString();
    $id1st = new Id($uuidString);
    $id2nd = new Id($uuidString);
    $id3rd = Id::create();

    expect($id1st->equals($id2nd))->toBeTrue()
        ->and($id1st->equals($id3rd))->toBeFalse();
});
