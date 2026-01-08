<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\TwoFactorNotEnabledException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\Services\TwoFactorService;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(RegenerateTwoFactorRecoveryCodesCommand::class)]
final readonly class RegenerateTwoFactorRecoveryCodesHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private TwoFactorService $twoFactorService,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws TwoFactorNotEnabledException
     */
    public function handle(RegenerateTwoFactorRecoveryCodesCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        if (!$user->hasTwoFactorEnabled()) {
            throw new TwoFactorNotEnabledException('Two-factor authentication is not enabled');
        }

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        $user->regenerateRecoveryCodes($recoveryCodes);

        $this->userRepository->save($user);
    }
}
