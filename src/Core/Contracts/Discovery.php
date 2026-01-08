<?php

declare(strict_types=1);

namespace Modules\Core\Contracts;

interface Discovery
{
    public function boot(): void;

    public function discover(?string $path = null): void;
}
