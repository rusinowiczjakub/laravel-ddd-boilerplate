<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\VerifyEmailCodeCommand;
use Modules\IAM\Domain\Exceptions\InvalidVerificationCodeException;
use Modules\IAM\Domain\Services\PostAuthenticationRedirectResolver;

class VerifyEmailCodeController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly PostAuthenticationRedirectResolver $redirectResolver,
    ) {
    }

    /**
     * Verify the email verification code.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:8'],
        ]);

        try {
            $this->commandBus->dispatch(new VerifyEmailCodeCommand(
                email: $request->user()->email,
                code: $request->code
            ));

            return redirect($this->redirectResolver->resolve($request->user()->id));
        } catch (InvalidVerificationCodeException $e) {
            throw ValidationException::withMessages([
                'code' => $e->getMessage(),
            ]);
        }
    }
}
