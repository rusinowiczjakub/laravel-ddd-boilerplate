<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Protect Horizon with secret - store in session after first valid request
        Horizon::auth(function ($request) {
            $secret = config('app.horizon_secret');

            // No secret configured - deny all
            if (empty($secret)) {
                return false;
            }

            // Check if secret provided in URL - if valid, store in session
            if ($request->has('secret') && $request->get('secret') === $secret) {
                session(['horizon_authorized' => true]);
                return true;
            }

            // Check if already authorized via session
            return session('horizon_authorized', false);
        });
    }

    /**
     * Register the Horizon gate.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            return true; // Auth handled in boot() via Horizon::auth
        });
    }
}
