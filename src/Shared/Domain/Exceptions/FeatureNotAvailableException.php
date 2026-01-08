<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

use RuntimeException;

final class FeatureNotAvailableException extends RuntimeException
{
    public static function forSmsChannel(): self
    {
        return new self('SMS channel is not available yet. Contact support to enable it for your workspace.');
    }

    public static function forPushChannel(): self
    {
        return new self('Push notifications are not available yet. Contact support to enable it for your workspace.');
    }

    public static function forSmsTemplate(): self
    {
        return new self('SMS templates are not available yet. Contact support to enable SMS for your workspace.');
    }

    public static function forPushTemplate(): self
    {
        return new self('Push templates are not available yet. Contact support to enable Push for your workspace.');
    }

    public static function forSmsProvider(): self
    {
        return new self('SMS providers are not available yet. Contact support to enable SMS for your workspace.');
    }

    public static function forPushProvider(): self
    {
        return new self('Push providers are not available yet. Contact support to enable Push for your workspace.');
    }
}
