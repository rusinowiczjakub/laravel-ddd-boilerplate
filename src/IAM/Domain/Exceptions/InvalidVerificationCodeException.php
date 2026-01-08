<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Exceptions;

use Exception;

final class InvalidVerificationCodeException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid or expired verification code');
    }
}
