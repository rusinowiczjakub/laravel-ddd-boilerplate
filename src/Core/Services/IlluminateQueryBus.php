<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Contracts\Bus\Dispatcher;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\Core\Query\Contracts\Query;

class IlluminateQueryBus implements QueryBus
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {
    }

    public function dispatch(Query $query): mixed
    {
        return $this->dispatcher->dispatch($query);
    }
}
