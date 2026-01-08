<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(DisableTwoFactorCommand::class)]
final readonly class DisableTwoFactorHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function handle(DisableTwoFactorCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $user->disableTwoFactor();

        $this->userRepository->save($user);
    }
}
