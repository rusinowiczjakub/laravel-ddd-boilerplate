<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Events;

use Modules\Core\Events\DomainEvent;

final class UserRegistered extends DomainEvent
{
    public function __construct(
        public string $userId,
        public readonly string $email,
        public readonly string $name,
    ) {
        parent::__construct($userId, 'user');
    }

    public function toPayload(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['user_id'],
            $payload['email'],
            $payload['name'],
        );
    }
}
