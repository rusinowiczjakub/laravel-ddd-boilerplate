<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Models;

use Modules\Shared\Domain\RateLimiting\RateLimitAction;

enum Plan: string
{
    case FREE = 'free';
    case STARTER = 'starter';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public static function withoutEnterprise(): array
    {
        return [self::FREE, self::STARTER, self::PRO];
    }

    /**
     * Per-minute API request rate limit.
     */
    public function requestsPerMinuteLimit(): int
    {
        return match($this) {
            self::FREE => 100,
            self::STARTER => 500,
            self::PRO => 2_000,
            self::ENTERPRISE => PHP_INT_MAX,
        };
    }

    public function eventLimit(): int
    {
        return match($this) {
            self::FREE => 1_000,
            self::STARTER => 10_000,
            self::PRO => 100_000,
            self::ENTERPRISE => PHP_INT_MAX,
        };
    }

    public function workflowsLimit(): int
    {
        return match($this) {
            self::FREE => 5,
            self::STARTER => 20,
            self::PRO, self::ENTERPRISE => PHP_INT_MAX,
        };
    }

    public function membersLimit(): int
    {
        return match($this) {
            self::FREE => 1,
            self::STARTER => 5,
            self::PRO => 15,
            self::ENTERPRISE => PHP_INT_MAX,
        };
    }

    /**
     * Email/notification sending limit per month.
     * ALWAYS unlimited - clients use their own providers (SendGrid, Mailgun, etc.)
     * We only limit events, not notifications.
     */
    public function emailLimit(): int
    {
        return PHP_INT_MAX;
    }

    /**
     * SMS sending limit per month.
     * ALWAYS unlimited - clients use their own providers (Twilio, etc.)
     * We only limit events, not SMS.
     */
    public function smsLimit(): int
    {
        return PHP_INT_MAX;
    }

    /**
     * Log retention in days.
     */
    public function logRetentionDays(): int
    {
        return match($this) {
            self::FREE => 7,
            self::STARTER => 30,
            self::PRO => 90,
            self::ENTERPRISE => PHP_INT_MAX,
        };
    }

    /**
     * Czy plan pozwala na overage (soft limit) czy hard block?
     */
    public function allowsOverage(): bool
    {
        return match($this) {
            self::FREE, self::STARTER, self::PRO, self::ENTERPRISE => false,
        };
    }

    public function limitFor(RateLimitAction $action): int
    {
        return match ($action) {
            RateLimitAction::EVENTS => $this->eventLimit(),
            RateLimitAction::WORKFLOWS => $this->workflowsLimit(),
            RateLimitAction::API_REQUESTS => $this->requestsPerMinuteLimit(),
            default => PHP_INT_MAX,
        };
    }

    public function price(): int
    {
        return match($this) {
            self::FREE => 0,
            self::STARTER => 19,
            self::PRO => 49,
            self::ENTERPRISE => 199,
        };
    }

    public function displayName(): string
    {
        return match($this) {
            self::FREE => 'Free',
            self::STARTER => 'Starter',
            self::PRO => 'Pro',
            self::ENTERPRISE => 'Enterprise',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::FREE => 'For personal projects',
            self::STARTER => 'For small teams',
            self::PRO => 'For growing businesses',
            self::ENTERPRISE => 'For enterprises',
        };
    }

    /**
     * @return string[]
     */
    public function features(): array
    {
        return match($this) {
            self::FREE => [
                '1,000 events per month',
                '5 workflows',
                '1 team member',
                '7 days log retention',
                'Community support',
                'watermark in notifications'
            ],
            self::STARTER => [
                '10,000 events per month',
                '20 workflows',
                '5 team members',
                '30 days log retention',
                'Email support',
            ],
            self::PRO => [
                '100,000 events per month',
                'Unlimited workflows',
                '15 team members',
                '90 days log retention',
                'Priority support',
            ],
            self::ENTERPRISE => [
                'Unlimited events',
                'Unlimited workflows',
                'Unlimited team members',
                'Unlimited log retention',
                'Dedicated infrastructure',
                '24/7 support',
            ],
        };
    }

    public function isRecommended(): bool
    {
        return $this === self::PRO;
    }

    public function isFree(): bool
    {
        return $this === self::FREE;
    }

    public function requiresWatermark(): bool
    {
        return $this->isFree();
    }
}
