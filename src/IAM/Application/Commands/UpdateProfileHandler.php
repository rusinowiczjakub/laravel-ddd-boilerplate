<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(UpdateProfileCommand::class)]
final readonly class UpdateProfileHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function handle(UpdateProfileCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $user->updateName($command->name);

        $this->userRepository->save($user);
    }
}
