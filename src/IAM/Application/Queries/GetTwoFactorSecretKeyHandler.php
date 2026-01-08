<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Queries;

use Modules\Core\Attributes\QueryHandler;
use Modules\IAM\Domain\Exceptions\TwoFactorNotEnabledException;
use Modules\IAM\Domain\Exceptions\UserNotFoundException;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\Shared\Domain\ValueObjects\Id;

#[QueryHandler(GetTwoFactorSecretKeyQuery::class)]
final readonly class GetTwoFactorSecretKeyHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     * @throws TwoFactorNotEnabledException
     */
    public function __invoke(GetTwoFactorSecretKeyQuery $query): string
    {
        $user = $this->userRepository->findById(Id::fromString($query->userId));

        if ($user === null) {
            throw new UserNotFoundException('User not found');
        }

        if ($user->twoFactorSecret() === null) {
            throw new TwoFactorNotEnabledException('Two-factor authentication is not enabled');
        }

        return $user->twoFactorSecret();
    }
}
