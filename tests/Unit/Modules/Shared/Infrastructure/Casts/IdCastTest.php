<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Shared\Infrastructure\Casts\IdCast;
use Modules\Shared\Infrastructure\Exception\InvalidCastValueException;

it('casts string to Id on get', function () {
    $cast = new IdCast;
    $model = new class extends Model {};
    $uuid = '123e4567-e89b-12d3-a456-426614174000';

    $result = $cast->get($model, 'id', $uuid, []);

    expect($result)->toBeInstanceOf(Id::class);
    expect($result->value())->toBe($uuid);
});

it('casts Id to string on set', function () {
    $cast = new IdCast;
    $model = new class extends Model {};
    $id = new Id('123e4567-e89b-12d3-a456-426614174000');

    $result = $cast->set($model, 'id', $id, []);

    expect($result)->toBe('123e4567-e89b-12d3-a456-426614174000');
});

it('throws exception when setting non-Id value', function () {
    $cast = new IdCast;
    $model = new class extends Model {};

    $cast->set($model, 'id', 'not-an-id-object', []);
})->throws(InvalidCastValueException::class, 'Value must be an instance of Id, given string');

it('throws exception when setting null value', function () {
    $cast = new IdCast;
    $model = new class extends Model {};

    $cast->set($model, 'id', null, []);
})->throws(InvalidCastValueException::class, 'Value must be an instance of Id, given NULL');

it('throws exception when setting object of wrong type', function () {
    $cast = new IdCast;
    $model = new class extends Model {};
    $wrongObject = new \stdClass;

    $cast->set($model, 'id', $wrongObject, []);
})->throws(InvalidCastValueException::class, 'Value must be an instance of Id, given stdClass');
