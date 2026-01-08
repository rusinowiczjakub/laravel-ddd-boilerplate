<?php

use App\Http\Middleware\CheckChannelFeature;
use App\Http\Middleware\CheckPlanLimit;
use App\Http\Middleware\CheckWaitlistMode;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Modules\Shared\Domain\Exceptions\DomainException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            TrustProxies::class
        ]);

        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'check-plan' => CheckPlanLimit::class,
            'check-waitlist' => CheckWaitlistMode::class,
            'check-channel-feature' => CheckChannelFeature::class,
        ]);
    })->withExceptions(function (Exceptions $exceptions) {
//         Handle domain exceptions with flash error messages for Inertia
        $exceptions->renderable(function (DomainException $e) {
            // Check if it's an Inertia request
            if (request()->header('X-Inertia')) {
                return back()->with('error', $e->getMessage());
            }

            // For regular web requests, also redirect with flash
            if (request()->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        });
    })->create();
