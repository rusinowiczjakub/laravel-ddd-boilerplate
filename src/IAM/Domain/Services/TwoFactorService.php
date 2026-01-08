<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Services;

interface TwoFactorService
{
    /**
     * Generate a new TOTP secret key.
     */
    public function generateSecret(): string;

    /**
     * Generate recovery codes.
     *
     * @return array<string>
     */
    public function generateRecoveryCodes(int $count = 8): array;

    /**
     * Verify a TOTP code against a secret.
     */
    public function verifyCode(string $secret, string $code): bool;

    /**
     * Generate a QR code URL for the authenticator app.
     */
    public function getQrCodeUrl(string $email, string $secret): string;

    /**
     * Generate a QR code SVG for the authenticator app.
     */
    public function getQrCodeSvg(string $email, string $secret): string;
}
