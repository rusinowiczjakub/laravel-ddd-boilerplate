<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Models;

use Modules\Core\Models\Casts\AsDate;
use MongoDB\Laravel\Eloquent\Model;

class EventStore extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'event_id',
        'aggregate_id',
        'aggregate_type',
        'aggregate_version',
        'event',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => AsDate::class,
    ];
}
