<?php

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/auth.php';

// Waitlist route (accessible even when waitlist mode is enabled)
Route::get('/waitlist', [WaitlistController::class, 'show'])->name('waitlist');
Route::post('/waitlist', [WaitlistController::class, 'store'])->name('waitlist.store');

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Invitation acceptance (public route - handles auth internally)
Route::get('invitations/accept/{token}', App\Http\Controllers\Invitations\AcceptInvitation::class)->name('invitations.accept');

Route::middleware(['auth', 'verified'])->group(function () {
    // Onboarding routes (after email verification)
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('create-workspace', [OnboardingController::class, 'createWorkspace'])->name('create-workspace');
        Route::get('select-plan', [OnboardingController::class, 'selectPlan'])->name('select-plan');
        Route::get('invite-team', [OnboardingController::class, 'inviteTeam'])->name('invite-team');
        Route::get('checkout', [OnboardingController::class, 'checkout'])->name('checkout');
    });

    // Workspace routes
    Route::post('workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::patch('workspaces/{workspaceId}', App\Http\Controllers\Workspaces\UpdateWorkspace::class)->name('workspaces.update');
    Route::post('workspaces/{workspaceId}/invite', [WorkspaceController::class, 'invite'])->name('workspaces.invite');
    Route::post('workspaces/{workspaceId}/switch', App\Http\Controllers\Workspaces\SwitchWorkspace::class)->name('workspaces.switch');
    Route::post('workspaces/{workspaceId}/transfer', App\Http\Controllers\Workspaces\TransferOwnership::class)->name('workspaces.transfer');
    Route::patch('workspaces/{workspaceId}/members/{memberId}', App\Http\Controllers\Workspaces\ChangeMemberRole::class)->name('workspaces.members.update');
    Route::delete('workspaces/{workspaceId}/members/{memberId}', App\Http\Controllers\Workspaces\RemoveMember::class)->name('workspaces.members.destroy');
    Route::delete('workspaces/{workspaceId}/invitations/{invitationId}', App\Http\Controllers\Workspaces\CancelInvitation::class)->name('workspaces.invitations.destroy');

    // Dashboard (requires workspace)
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Settings routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', function () {
            return Inertia::render('settings/profile');
        })->name('profile');

        Route::patch('profile', App\Http\Controllers\Settings\UpdateProfile::class)
            ->name('profile.update');

        Route::post('profile/email', App\Http\Controllers\Settings\ChangeEmail::class)
            ->name('profile.email');

        Route::get('password', function () {
            return Inertia::render('settings/password');
        })->name('password');

        Route::put('password', App\Http\Controllers\Settings\UpdatePassword::class)
            ->name('password.update');

        Route::get('appearance', function () {
            return Inertia::render('settings/appearance');
        })->name('appearance');

        Route::get('workspace', App\Http\Controllers\Settings\ShowWorkspaceSettings::class)
            ->name('workspace');

        Route::get('billing', App\Http\Controllers\Settings\ShowBillingSettings::class)
            ->name('billing');

        Route::get('usage', App\Http\Controllers\Settings\ShowUsageSettings::class)
            ->name('usage');
    });

    // Billing routes
    Route::get('billing/change-plan', App\Http\Controllers\Billing\ShowChangePlan::class)
        ->name('billing.change-plan');
    Route::post('billing/portal', App\Http\Controllers\Billing\RedirectToBillingPortal::class)
        ->name('billing.portal');

    // Feedback
    Route::post('feedback', App\Http\Controllers\SendFeedbackController::class)
        ->name('feedback.send');

    // Two-Factor Authentication routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::post('two-factor-authentication', App\Http\Controllers\TwoFactor\EnableTwoFactor::class)
            ->name('two-factor.enable');
        Route::post('confirmed-two-factor-authentication', App\Http\Controllers\TwoFactor\ConfirmTwoFactor::class)
            ->name('two-factor.confirm');
        Route::delete('two-factor-authentication', App\Http\Controllers\TwoFactor\DisableTwoFactor::class)
            ->name('two-factor.disable');
        Route::get('two-factor-qr-code', App\Http\Controllers\TwoFactor\GetTwoFactorQrCode::class)
            ->name('two-factor.qr-code');
        Route::get('two-factor-secret-key', App\Http\Controllers\TwoFactor\GetTwoFactorSecretKey::class)
            ->name('two-factor.secret-key');
        Route::get('two-factor-recovery-codes', App\Http\Controllers\TwoFactor\GetTwoFactorRecoveryCodes::class)
            ->name('two-factor.recovery-codes');
        Route::post('two-factor-recovery-codes', App\Http\Controllers\TwoFactor\RegenerateTwoFactorRecoveryCodes::class)
            ->name('two-factor.regenerate-recovery-codes');
    });

    // Keep old route name for backwards compatibility
    Route::get('profile', fn() => redirect()->route('settings.profile'))->name('profile.edit');

    // Email preview (tylko dla developmentu!)
    Route::get('/email-preview', function () {
        return new \App\Mail\VerificationCodeMail(
            code: 'L3U7OAPH',
            expiresIn: 15
        );
    });
});

