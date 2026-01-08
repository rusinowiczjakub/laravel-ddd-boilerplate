<?php

declare(strict_types=1);

namespace Modules\Core\Bus\Contracts;

use Modules\Core\Command\Contracts\Command;

interface CommandBus
{
    public function dispatch(Command $command): mixed;
}
