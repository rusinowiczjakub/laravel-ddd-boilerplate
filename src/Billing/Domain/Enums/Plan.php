<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Enums;

enum Plan: string
{
    case FREE = 'free';
    case STARTER = 'starter';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public function displayName(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::STARTER => 'Starter',
            self::PRO => 'Pro',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function isPaid(): bool
    {
        return $this !== self::FREE;
    }

    public function stripePriceId(string $billingPeriod = 'monthly'): ?string
    {
        if ($billingPeriod === 'yearly') {
            return match ($this) {
                self::FREE => null,
                self::STARTER => config('services.stripe.price_starter_yearly'),
                self::PRO => config('services.stripe.price_pro_yearly'),
                self::ENTERPRISE => null,
            };
        }

        // Default: monthly
        return match ($this) {
            self::FREE => null,
            self::STARTER => config('services.stripe.price_starter'),
            self::PRO => config('services.stripe.price_pro'),
            self::ENTERPRISE => null,
        };
    }
}
