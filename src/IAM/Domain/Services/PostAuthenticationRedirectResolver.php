<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Services;

use Modules\Shared\Domain\ValueObjects\Id;

interface PostAuthenticationRedirectResolver
{
    /**
     * Determine where to redirect user after authentication/verification.
     *
     * Priority:
     * 1. Pending invitation → invitations.accept
     * 2. User has workspaces → dashboard
     * 3. No workspaces → onboarding.create-workspace
     */
    public function resolve(Id $userId): string;
}
