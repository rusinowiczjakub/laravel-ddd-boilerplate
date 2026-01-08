<?php

declare(strict_types=1);

namespace Modules\Core\Contracts;

interface StoreReconstitutable
{
    public static function reconstituteFromStore(array $data): self;
}
