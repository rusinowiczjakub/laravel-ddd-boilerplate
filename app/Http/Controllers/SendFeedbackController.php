<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class SendFeedbackController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:bug,feature,general'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'screenshot' => ['nullable', 'file', 'image', 'max:10240'], // 10MB
            'url' => ['required', 'string'],
            'userAgent' => ['required', 'string'],
        ]);

        $webhookUrl = config('services.discord.feedback_webhook');

        if (!$webhookUrl) {
            Log::warning('Discord feedback webhook not configured');
            return back()->with('error', 'Feedback system not configured');
        }

        $user = $request->user();
        $workspaceId = session('current_workspace_id');
        $workspace = $workspaceId ? \DB::table('workspaces')->where('id', $workspaceId)->first() : null;

        // Build Discord embed
        $typeEmojis = [
            'bug' => '🐛',
            'feature' => '💡',
            'general' => '💬',
        ];

        $typeLabels = [
            'bug' => 'Bug Report',
            'feature' => 'Feature Request',
            'general' => 'General Feedback',
        ];

        $typeColors = [
            'bug' => 0xEF4444,    // red
            'feature' => 0x8B5CF6, // purple
            'general' => 0x3B82F6, // blue
        ];

        $type = $request->input('type');

        $embed = [
            'title' => $typeEmojis[$type] . ' ' . $typeLabels[$type],
            'description' => $request->input('message'),
            'color' => $typeColors[$type],
            'fields' => [
                [
                    'name' => 'URL',
                    'value' => $request->input('url'),
                    'inline' => false,
                ],
                [
                    'name' => 'User Agent',
                    'value' => substr($request->input('userAgent'), 0, 1000),
                    'inline' => false,
                ],
            ],
            'timestamp' => now()->toIso8601String(),
        ];

        // Add user info if logged in
        if ($user) {
            $embed['fields'][] = [
                'name' => 'User Info',
                'value' => "**Name:** {$user->name}\n**Email:** {$user->email}\n**User ID:** `{$user->id}`" .
                    ($workspace ? "\n**Workspace:** {$workspace->name}\n**Workspace ID:** `{$workspaceId}`" : ''),
                'inline' => false,
            ];
        }

        // Handle screenshot upload
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');

            // For Discord, we need to send as multipart form
            $response = Http::attach(
                'file',
                file_get_contents($file->getRealPath()),
                'screenshot.' . $file->getClientOriginalExtension()
            )->post($webhookUrl, [
                'payload_json' => json_encode([
                    'embeds' => [$embed],
                ]),
            ]);
        } else {
            $response = Http::post($webhookUrl, [
                'embeds' => [$embed],
            ]);
        }

        if ($response->successful()) {
            return back()->with('success', 'Thank you for your feedback!');
        }

        Log::error('Failed to send feedback to Discord', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return back()->with('error', 'Failed to send feedback');
    }
}
