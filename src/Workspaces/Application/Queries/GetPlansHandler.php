<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Modules\Core\Attributes\QueryHandler;
use Modules\Workspaces\Application\Responses\PlanResponse;
use Modules\Workspaces\Application\Responses\PlansListResponse;
use Modules\Workspaces\Domain\Models\Plan;

#[QueryHandler(GetPlansQuery::class)]
final readonly class GetPlansHandler
{
    public function handle(GetPlansQuery $query): PlansListResponse
    {
        $plans = array_map(
            fn(Plan $plan) => PlanResponse::fromPlan($plan),
            Plan::withoutEnterprise()
        );

        return new PlansListResponse(plans: $plans);
    }
}
