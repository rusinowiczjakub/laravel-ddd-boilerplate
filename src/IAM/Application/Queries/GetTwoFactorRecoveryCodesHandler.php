<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Queries;

use Modules\Core\Attributes\QueryHandler;
use Modules\IAM\Domain\Exceptions\TwoFactorNotEnabledException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\Shared\Domain\ValueObjects\Id;

#[QueryHandler(GetTwoFactorRecoveryCodesQuery::class)]
final readonly class GetTwoFactorRecoveryCodesHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws TwoFactorNotEnabledException
     * @return array<string>
     */
    public function __invoke(GetTwoFactorRecoveryCodesQuery $query): array
    {
        $user = $this->userRepository->findById(Id::fromString($query->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        if (!$user->hasTwoFactorEnabled()) {
            throw new TwoFactorNotEnabledException('Two-factor authentication is not enabled');
        }

        return $user->twoFactorRecoveryCodes() ?? [];
    }
}
