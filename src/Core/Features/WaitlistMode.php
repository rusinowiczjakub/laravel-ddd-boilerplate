<?php

declare(strict_types=1);

namespace Modules\Core\Features;

/**
 * Feature flag for Waitlist Mode.
 *
 * When enabled, registration and login are disabled.
 * Users can only sign up for the waitlist.
 */
class WaitlistMode
{
    /**
     * Resolve the feature's initial value.
     *
     * Returns true when waitlist mode is enabled (default: enabled).
     * This blocks normal registration/login and shows waitlist form instead.
     */
    public function resolve(mixed $scope): bool
    {
        return config('features.waitlist_mode', false);
    }
}
