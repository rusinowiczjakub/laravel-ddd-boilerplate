<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Repositories\EmailVerificationSessionRepository;
use Modules\IAM\Domain\ValueObjects\Email;

#[CommandHandler(SendEmailVerificationCodeCommand::class)]
final readonly class SendEmailVerificationCodeHandler
{
    public function __construct(
        private EmailVerificationSessionRepository $sessionRepository,
    ) {
    }

    public function __invoke(SendEmailVerificationCodeCommand $command): void
    {
        $email = Email::fromString($command->email);

        $code = $this->sessionRepository->createSession($email);

        // Wyślij email z kodem
        Mail::to($email->value())
            ->send(new VerificationCodeMail(
                code: $code->value(),
                expiresIn: 15
            ));
    }
}
