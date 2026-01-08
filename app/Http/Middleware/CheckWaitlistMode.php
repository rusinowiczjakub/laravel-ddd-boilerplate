<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Pennant\Feature;
use Modules\Core\Features\WaitlistMode;
use Symfony\Component\HttpFoundation\Response;

class CheckWaitlistMode
{
    /**
     * Handle an incoming request.
     *
     * If waitlist mode is enabled, redirect to waitlist page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Feature::active(WaitlistMode::class)) {
            return Inertia::location(route('waitlist'));
        }

        return $next($request);
    }
}
