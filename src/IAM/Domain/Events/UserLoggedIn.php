<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Events;

use Modules\Core\Events\DomainEvent;

final class UserLoggedIn extends DomainEvent
{
    public function __construct(
        string $userId,
        public readonly string $email,
    ) {
        parent::__construct($userId, 'user');
    }

    public function toPayload(): array
    {
        return [
            'email' => $this->email,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['user_id'],
            $payload['email'],
        );
    }
}
