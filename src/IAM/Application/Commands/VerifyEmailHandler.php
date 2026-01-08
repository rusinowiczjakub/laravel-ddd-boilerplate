<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\IAM\Domain\Events\EmailVerified;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(VerifyEmailCommand::class)]
final readonly class VerifyEmailHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function handle(VerifyEmailCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->verifyEmail();

        $this->userRepository->save($user);

        $this->eventBus->dispatch(
            new EmailVerified(
                userId: $user->id()->value(),
                email: $user->email()->value,
            )
        );
    }
}
