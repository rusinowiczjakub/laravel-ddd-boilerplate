<?php

declare(strict_types=1);

namespace Modules\IAM\Infrastructure\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\IAM\Domain\Repositories\EmailVerificationSessionRepository;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\EmailVerificationCode;

final class DatabaseEmailVerificationSessionRepository implements EmailVerificationSessionRepository
{
    private const TABLE = 'email_verification_sessions';
    private const EXPIRATION_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;

    public function createSession(Email $email): EmailVerificationCode
    {
        $code = EmailVerificationCode::generate();

        // Usuń starą sesję jeśli istnieje
        $this->deleteSession($email);

        // Utwórz nową sesję
        DB::table(self::TABLE)->insert([
            'email' => $email->value(),
            'code' => $code->value(),
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes(self::EXPIRATION_MINUTES),
            'created_at' => Carbon::now(),
        ]);

        return $code;
    }

    public function findByEmail(Email $email): ?array
    {
        $session = DB::table(self::TABLE)
            ->where('email', $email->value())
            ->first();

        return $session ? (array) $session : null;
    }

    public function verify(Email $email, EmailVerificationCode $code): bool
    {
        $session = $this->findByEmail($email);

        if (!$session) {
            return false;
        }

        // Sprawdź czy sesja wygasła
        if (Carbon::parse($session['expires_at'])->isPast()) {
            $this->deleteSession($email);
            return false;
        }

        // Sprawdź czy przekroczono limit prób
        if ($session['attempts'] >= self::MAX_ATTEMPTS) {
            return false;
        }

        // Zwiększ licznik prób
        $this->incrementAttempts($email);

        // Sprawdź poprawność kodu
        return $session['code'] === $code->value();
    }

    public function incrementAttempts(Email $email): void
    {
        DB::table(self::TABLE)
            ->where('email', $email->value())
            ->increment('attempts');
    }

    public function deleteSession(Email $email): void
    {
        DB::table(self::TABLE)
            ->where('email', $email->value())
            ->delete();
    }
}
