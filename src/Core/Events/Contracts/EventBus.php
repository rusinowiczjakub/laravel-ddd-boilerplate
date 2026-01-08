<?php

declare(strict_types=1);

namespace Modules\Core\Events\Contracts;

interface EventBus
{
    public function dispatch(Event ...$events): void;
}
