<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\HashedPassword;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(ChangePasswordCommand::class)]
final readonly class ChangePasswordHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidCredentialsException
     */
    public function handle(ChangePasswordCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        if (!$user->verifyPassword($command->currentPassword)) {
            throw new InvalidCredentialsException('Current password is incorrect');
        }

        $user->changePassword(HashedPassword::fromPlaintext($command->newPassword));

        $this->userRepository->save($user);
    }
}
