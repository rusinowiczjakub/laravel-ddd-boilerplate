<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Listeners;

use Illuminate\Auth\Events\Registered;
use Modules\Core\Attributes\Subscribe;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Events\Contracts\AsyncListener;
use Modules\IAM\Application\Commands\SendEmailVerificationCodeCommand;
use Modules\IAM\Domain\Events\EmailChanged;
use Modules\IAM\Domain\Events\UserRegistered;
use Modules\Shared\Domain\Models\Queues;

#[Subscribe([UserRegistered::class, EmailChanged::class])]
final class SendEmailVerificationCode implements AsyncListener
{
    public function __construct(
        private readonly CommandBus $commandBus
    )
    {
    }

    public function __invoke(UserRegistered|EmailChanged $event): void
    {
        $email = match (true) {
            $event instanceof UserRegistered => $event->email,
            $event instanceof EmailChanged => $event->newEmail,
        };

        $this->commandBus->dispatch(new SendEmailVerificationCodeCommand(
            email: $email
        ));
    }

    public function viaQueue(): string
    {
        return Queues::APP_NOTIFICATIONS->value;
    }
}
