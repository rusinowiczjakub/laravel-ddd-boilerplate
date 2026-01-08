<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

use Exception;
use Throwable;

class DomainException extends Exception
{
    private readonly array $context;

    public function __construct(string $message = '', ?array $context = [], int $code = 0, ?Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
