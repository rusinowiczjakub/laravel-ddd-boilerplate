<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\IAM\Domain\Exceptions\InvalidVerificationCodeException;
use Modules\IAM\Domain\Repositories\EmailVerificationSessionRepository;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\EmailVerificationCode;
use RuntimeException;

#[CommandHandler(VerifyEmailCodeCommand::class)]
final readonly class VerifyEmailCodeHandler
{
    public function __construct(
        private EmailVerificationSessionRepository $sessionRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(VerifyEmailCodeCommand $command): void
    {
        $email = Email::fromString($command->email);
        $code = EmailVerificationCode::fromString($command->code);

        // Zweryfikuj kod w sesji
        $isValid = $this->sessionRepository->verify($email, $code);

        if (!$isValid) {
            throw new InvalidVerificationCodeException();
        }

        // Znajdź użytkownika i zweryfikuj email
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new RuntimeException('User not found');
        }

        $user->verifyEmail();
        $this->userRepository->save($user);

        // Usuń sesję weryfikacyjną
        $this->sessionRepository->deleteSession($email);
    }
}
