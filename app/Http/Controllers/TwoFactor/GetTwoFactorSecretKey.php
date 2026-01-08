<?php

declare(strict_types=1);

namespace App\Http\Controllers\TwoFactor;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\IAM\Application\Queries\GetTwoFactorSecretKeyQuery;

final readonly class GetTwoFactorSecretKey
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $secretKey = $this->queryBus->dispatch(new GetTwoFactorSecretKeyQuery(
            userId: $request->user()->id->value(),
        ));

        return response()->json([
            'secretKey' => $secretKey,
        ]);
    }
}
