<?php

namespace App\Http\Middleware;

use App\Services\WorkspaceCache;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Laravel\Pennant\Feature;
use Modules\Core\Features\WaitlistMode;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $workspaces = [];
        $currentWorkspace = null;
        $workspaceCache = app(WorkspaceCache::class);

        if ($request->user()) {
            // Get cached user's workspaces (basic info only)
            $workspaces = $workspaceCache->getUserWorkspaces($request->user()->id->value());

            // Get current workspace from session or use first one
            $currentWorkspaceId = session('current_workspace_id');
            if ($currentWorkspaceId) {
                $currentWorkspace = collect($workspaces)->firstWhere('id', $currentWorkspaceId);
            }

            if (!$currentWorkspace && count($workspaces) > 0) {
                $currentWorkspace = $workspaces[0];
                session(['current_workspace_id' => $workspaces[0]['id']]);
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'version' => config('app.version'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'workspaces' => $workspaces,
            'currentWorkspace' => $currentWorkspace,
            // Lazy load subscription data - only loaded when component accesses it
            'currentWorkspaceSubscription' => $currentWorkspace
                ? fn () => $workspaceCache->getWorkspaceSubscription($currentWorkspace['id'])
                : null,
            'waitlistMode' => fn () => Cache::remember(
                'feature.waitlist_mode',
                300, // 5 minutes
                fn () => Feature::active(WaitlistMode::class)
            ),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
