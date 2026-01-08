<?php

declare(strict_types=1);

namespace Modules\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Subscribe
{
    public function __construct(
        public string|array $target,
        public int $priority = 0,
        public string $queue = 'default'
    ) {
    }
}
