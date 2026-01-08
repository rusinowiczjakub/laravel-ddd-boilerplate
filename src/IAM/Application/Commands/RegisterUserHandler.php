<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\IAM\Application\Responses\UserRegisteredResponse;
use Modules\IAM\Domain\Exceptions\EmailAlreadyExistsException;
use Modules\IAM\Domain\Models\User;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\HashedPassword;

#[CommandHandler(RegisterUserCommand::class)]
final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws EmailAlreadyExistsException
     */
    public function handle(RegisterUserCommand $command): UserRegisteredResponse
    {
        $email = Email::fromString($command->email);

        if ($this->userRepository->emailExists($email)) {
            throw new EmailAlreadyExistsException('Email already exists');
        }

        $user = User::register(
            name: $command->name,
            email: $email,
            password: HashedPassword::fromPlaintext($command->password),
        );

        $this->userRepository->save($user);

        $this->eventBus->dispatch(
            ...$user->pullEvents()
        );

        return new UserRegisteredResponse(
            userId: $user->id(),
            email: $user->email(),
            name: $user->name(),
        );
    }
}
