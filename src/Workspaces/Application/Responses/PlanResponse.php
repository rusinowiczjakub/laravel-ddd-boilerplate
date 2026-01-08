<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

use Modules\Workspaces\Domain\Models\Plan;

final readonly class PlanResponse
{
    /**
     * @param string[] $features
     */
    public function __construct(
        public string $plan,
        public string $name,
        public string $description,
        public int $price,
        public array $features,
        public bool $recommended,
    ) {
    }

    public static function fromPlan(Plan $plan): self
    {
        return new self(
            plan: $plan->value,
            name: $plan->displayName(),
            description: $plan->description(),
            price: $plan->price(),
            features: $plan->features(),
            recommended: $plan->isRecommended(),
        );
    }
}
