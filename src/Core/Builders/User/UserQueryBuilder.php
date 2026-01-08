<?php

declare(strict_types=1);

namespace Modules\Core\Builders\User;

use Illuminate\Database\Eloquent\Builder;

class UserQueryBuilder extends Builder
{
    public function byEmail(string $email): self
    {
        return $this->where('email', $email);
    }
}
