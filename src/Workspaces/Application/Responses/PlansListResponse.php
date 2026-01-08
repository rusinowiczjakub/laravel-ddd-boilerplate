<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

final readonly class PlansListResponse
{
    /**
     * @param PlanResponse[] $plans
     */
    public function __construct(
        public array $plans,
    ) {
    }
}
