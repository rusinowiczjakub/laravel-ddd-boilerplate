<?php

declare(strict_types=1);

namespace Modules\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class QueryHandler
{
    public function __construct(
        public string $target
    ) {
    }
}
