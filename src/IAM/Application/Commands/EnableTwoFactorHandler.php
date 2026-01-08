<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\Services\TwoFactorService;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(EnableTwoFactorCommand::class)]
final readonly class EnableTwoFactorHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private TwoFactorService $twoFactorService,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function handle(EnableTwoFactorCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        $secret = $this->twoFactorService->generateSecret();
        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        $user->enableTwoFactor($secret, $recoveryCodes);

        $this->userRepository->save($user);
    }
}
