<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\IAM\Domain\Exceptions\EmailAlreadyExistsException;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(ChangeEmailCommand::class)]
final readonly class ChangeEmailHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidCredentialsException
     * @throws EmailAlreadyExistsException
     */
    public function handle(ChangeEmailCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        if (!$user->verifyPassword($command->password)) {
            throw new InvalidCredentialsException('Invalid password');
        }

        $newEmail = Email::fromString($command->newEmail);

        if ($this->userRepository->emailExists($newEmail)) {
            throw new EmailAlreadyExistsException('Email already in use');
        }

        $user->changeEmail($newEmail);

        $this->userRepository->save($user);

        $this->eventBus->dispatch(...$user->pullEvents());
    }
}
