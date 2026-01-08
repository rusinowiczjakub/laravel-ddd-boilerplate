<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\IAM\Domain\Events\PasswordWasReset;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\HashedPassword;

#[CommandHandler(ResetPasswordCommand::class)]
final readonly class ResetPasswordHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function handle(ResetPasswordCommand $command): void
    {
        $user = $this->userRepository->findByEmail(Email::fromString($command->email));

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        $user->changePassword(HashedPassword::fromPlaintext($command->password));

        $this->userRepository->save($user);

        $this->eventBus->dispatch(
            new PasswordWasReset(
                userId: $user->id()->value(),
                email: $user->email()->value,
            )
        );
    }
}
