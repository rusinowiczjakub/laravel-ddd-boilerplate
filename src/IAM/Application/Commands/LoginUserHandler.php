<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\IAM\Application\Responses\UserLoggedInResponse;
use Modules\IAM\Domain\Events\UserLoggedIn;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\Email;

#[CommandHandler(LoginUserCommand::class)]
final readonly class LoginUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function handle(LoginUserCommand $command): UserLoggedInResponse
    {
        $email = Email::fromString($command->email);
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$user->verifyPassword($command->password)) {
            throw new InvalidCredentialsException('Invalid credentials');
        }

        $this->eventBus->dispatch(
            new UserLoggedIn(
                userId: $user->id()->value(),
                email: $user->email()->value,
            )
        );

        return new UserLoggedInResponse(
            userId: $user->id(),
            email: $user->email(),
            name: $user->name(),
            remember: $command->remember,
        );
    }
}
