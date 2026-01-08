<?php

declare(strict_types=1);

namespace Modules\Core\Bus\Contracts;

use Modules\Core\Query\Contracts\Query;

interface QueryBus
{
    public function dispatch(Query $query): mixed;
}
