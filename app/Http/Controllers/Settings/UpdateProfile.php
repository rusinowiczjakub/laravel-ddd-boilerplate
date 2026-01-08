<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\UpdateProfileCommand;

final readonly class UpdateProfile
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
                $user->avatar = null;
                $user->save();
            }

            return back();
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => ['required', 'image', 'max:2048'],
            ]);

            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars/users', 'public');
            $user->avatar = $path;
            $user->save();

            return back();
        }

        // Handle name update
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $this->commandBus->dispatch(new UpdateProfileCommand(
            userId: $user->id,
            name: $request->input('name'),
        ));

        return back();
    }
}
