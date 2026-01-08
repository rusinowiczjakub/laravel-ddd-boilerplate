<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Events;

use Modules\Core\Events\Contracts\Event;
use Modules\Core\Events\DomainEvent;
use Modules\IAM\Domain\Models\User;

class EmailChanged extends DomainEvent
{
    public function __construct(
        public string $userId,
        public string $oldEmail,
        public string $newEmail,
    ) {
        parent::__construct($userId, User::class);
    }

    public function toPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'old_email' => $this->oldEmail,
            'new_email' => $this->newEmail,
        ];
    }

    public static function fromPayload(array $payload): Event
    {
        return new self(
            $payload['user_id'],
            $payload['old_email'],
            $payload['new_email'],
        );
    }
}
