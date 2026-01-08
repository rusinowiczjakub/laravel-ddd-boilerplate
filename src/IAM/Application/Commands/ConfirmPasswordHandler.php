<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(ConfirmPasswordCommand::class)]
final readonly class ConfirmPasswordHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidCredentialsException
     */
    public function handle(ConfirmPasswordCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        if (!$user->verifyPassword($command->password)) {
            throw new InvalidCredentialsException('Invalid password');
        }
    }
}
