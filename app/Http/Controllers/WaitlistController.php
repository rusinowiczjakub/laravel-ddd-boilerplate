<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WaitlistController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('waitlist');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Check if email already exists
        $exists = DB::table('waitlist')->where('email', $validated['email'])->exists();

        if (!$exists) {
            DB::table('waitlist')->insert([
                'email' => $validated['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back();
    }
}
