<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Modules\Billing\Application\Queries\GetEarlyBirdSlotsQuery;
use Modules\Core\Bus\Contracts\QueryBus;

final readonly class ShowPricing
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): Response
    {
        // Get current early-bird slots from Stripe
        $earlyBirdSlots = $this->queryBus->dispatch(new GetEarlyBirdSlotsQuery());

        return Inertia::render('pricing', [
            'earlyBirdSlots' => $earlyBirdSlots->toArray(),
        ]);
    }
}
