<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\InvalidTwoFactorCodeException;
use Modules\IAM\Domain\Exceptions\TwoFactorNotEnabledException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\Services\TwoFactorService;
use Modules\Shared\Domain\ValueObjects\Id;

#[CommandHandler(ConfirmTwoFactorCommand::class)]
final readonly class ConfirmTwoFactorHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private TwoFactorService $twoFactorService,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws TwoFactorNotEnabledException
     * @throws InvalidTwoFactorCodeException
     */
    public function handle(ConfirmTwoFactorCommand $command): void
    {
        $user = $this->userRepository->findById(Id::fromString($command->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        if ($user->twoFactorSecret() === null) {
            throw new TwoFactorNotEnabledException('Two-factor authentication is not enabled');
        }

        if (!$this->twoFactorService->verifyCode($user->twoFactorSecret(), $command->code)) {
            throw new InvalidTwoFactorCodeException('Invalid verification code');
        }

        $user->confirmTwoFactor();

        $this->userRepository->save($user);
    }
}
