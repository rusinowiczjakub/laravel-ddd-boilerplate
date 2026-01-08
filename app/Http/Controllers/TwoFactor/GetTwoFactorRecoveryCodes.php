<?php

declare(strict_types=1);

namespace App\Http\Controllers\TwoFactor;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\IAM\Application\Queries\GetTwoFactorRecoveryCodesQuery;

final readonly class GetTwoFactorRecoveryCodes
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $recoveryCodes = $this->queryBus->dispatch(new GetTwoFactorRecoveryCodesQuery(
            userId: $request->user()->id->value(),
        ));

        return response()->json([
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}
