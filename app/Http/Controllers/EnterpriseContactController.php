<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

final class EnterpriseContactController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'employees' => ['required', 'string'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        // Rate limiting: 3 submissions per hour per IP
        $key = 'enterprise-contact:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->with('error', "Too many requests. Please try again in " . ceil($seconds / 60) . " minutes.");
        }

        RateLimiter::hit($key, 3600); // 1 hour

        // Send to Discord webhook
        $webhookUrl = config('services.discord.enterprise_webhook');

        if ($webhookUrl) {
            try {
                $embed = [
                    'title' => '🏢 New Enterprise Contact',
                    'color' => 5763719, // Emerald color
                    'fields' => [
                        [
                            'name' => '👤 Name',
                            'value' => $validated['name'],
                            'inline' => true,
                        ],
                        [
                            'name' => '📧 Email',
                            'value' => $validated['email'],
                            'inline' => true,
                        ],
                        [
                            'name' => '🏢 Company',
                            'value' => $validated['company'],
                            'inline' => true,
                        ],
                        [
                            'name' => '👥 Company Size',
                            'value' => $validated['employees'],
                            'inline' => true,
                        ],
                    ],
                    'timestamp' => now()->toIso8601String(),
                ];

                if (!empty($validated['message'])) {
                    $embed['fields'][] = [
                        'name' => '💬 Message',
                        'value' => $validated['message'],
                        'inline' => false,
                    ];
                }

                Http::post($webhookUrl, [
                    'embeds' => [$embed],
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send Discord webhook', [
                    'error' => $e->getMessage(),
                    'data' => $validated,
                ]);
            }
        }

        return back()->with('success', "Thank you! We'll be in touch soon.");
    }
}
