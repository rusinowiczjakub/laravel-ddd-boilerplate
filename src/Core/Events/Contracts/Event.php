<?php

declare(strict_types=1);

namespace Modules\Core\Events\Contracts;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Uuid;

interface Event
{
    public function eventId(): Uuid;

    public function aggregateId(): string;

    public function aggregateType(): string;

    public function occurredAt(): Date;

    public function aggregateVersion(): ?int;

    public function withAggregateVersion(int $version): self;

    public function toPayload(): array;

    public static function fromPayload(array $payload): self;
}
