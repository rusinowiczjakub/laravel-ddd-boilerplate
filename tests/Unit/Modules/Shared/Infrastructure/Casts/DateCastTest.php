<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Infrastructure\Casts\DateCast;
use Modules\Shared\Infrastructure\Exception\InvalidCastValueException;

it('casts string to Date on get', function () {
    $cast = new DateCast;
    $model = new class extends Model {};
    $dateString = '2025-10-16 10:30:00';

    $result = $cast->get($model, 'created_at', $dateString, []);

    expect($result)->toBeInstanceOf(Date::class);
    expect($result->toString())->toBe($dateString);
});

it('casts Date to string on set', function () {
    $cast = new DateCast;
    $model = new class extends Model {};
    $date = new Date('2025-10-16 10:30:00');

    $result = $cast->set($model, 'created_at', $date, []);

    expect($result)->toBe('2025-10-16 10:30:00');
});

it('throws exception when setting non-Date value', function () {
    $cast = new DateCast;
    $model = new class extends Model {};

    $cast->set($model, 'created_at', 'not-a-date-object', []);
})->throws(InvalidCastValueException::class, 'Value must be an instance of Date, given string');

it('throws exception when setting null value', function () {
    $cast = new DateCast;
    $model = new class extends Model {};

    $cast->set($model, 'created_at', null, []);
})->throws(InvalidCastValueException::class, 'Value must be an instance of Date, given NULL');

it('throws exception when setting object of wrong type', function () {
    $cast = new DateCast;
    $model = new class extends Model {};
    $wrongObject = new \stdClass;

    $cast->set($model, 'created_at', $wrongObject, []);
})->throws(InvalidCastValueException::class, 'Value must be an instance of Date, given stdClass');
