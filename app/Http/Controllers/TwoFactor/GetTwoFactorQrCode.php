<?php

declare(strict_types=1);

namespace App\Http\Controllers\TwoFactor;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\IAM\Application\Queries\GetTwoFactorQrCodeQuery;

final readonly class GetTwoFactorQrCode
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $svg = $this->queryBus->dispatch(new GetTwoFactorQrCodeQuery(
            userId: $request->user()->id->value(),
        ));

        return response()->json([
            'svg' => $svg,
        ]);
    }
}
