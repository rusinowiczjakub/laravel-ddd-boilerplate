<?php

declare(strict_types=1);

namespace Modules\IAM\Infrastructure\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use Modules\IAM\Domain\Services\TwoFactorService;
use PragmaRX\Google2FA\Google2FA;

final readonly class Google2FATwoFactorService implements TwoFactorService
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];

        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::upper(Str::random(8));
        }

        return $codes;
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    public function getQrCodeUrl(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            company: config('app.name', 'Laravel boilerplate'),
            holder: $email,
            secret: $secret,
        );
    }

    public function getQrCodeSvg(string $email, string $secret): string
    {
        $url = $this->getQrCodeUrl($email, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(192),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return $writer->writeString($url);
    }
}
