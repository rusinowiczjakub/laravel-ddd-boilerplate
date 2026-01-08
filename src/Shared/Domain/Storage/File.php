<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Storage;

use RuntimeException;

use function pathinfo;
use function strtolower;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class File
{
    public function __construct(
        private readonly string $path,
        private readonly ?string $mimeType = null,
        private readonly ?int $size = null,
        private readonly ?string $originalName = null
    ) {
    }

    public function extensionOr(string $fallback = 'bin'): string
    {
        $ext = pathinfo($this->originalName ?? $this->path, PATHINFO_EXTENSION);

        return $ext ? strtolower($ext) : $fallback;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function mimeType(): ?string
    {
        return $this->mimeType;
    }

    public function size(): ?int
    {
        return $this->size;
    }

    public function originalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * Tworzy nowy tymczasowy plik i zwraca obiekt File z tymczasową ścieżką.
     *
     * @throws RuntimeException jeśli nie można utworzyć tymczasowego pliku
     */
    public static function createTemp(?string $extension = null): self
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'file_');

        if ($tempPath === false) {
            throw new RuntimeException('Unable to create temporary file');
        }

        // Dodaj rozszerzenie jeśli podane
        if ($extension !== null) {
            $tempPathWithExt = $tempPath . '.' . $extension;
            if (! rename($tempPath, $tempPathWithExt)) {
                unlink($tempPath);
                throw new RuntimeException("Unable to rename temporary file to add extension: {$extension}");
            }
            $tempPath = $tempPathWithExt;
        }

        return new self(
            path: $tempPath,
            mimeType: null,
            size: 0,
            originalName: null
        );
    }

    /**
     * Usuwa plik z dysku.
     *
     * @throws RuntimeException jeśli plik nie może być usunięty
     */
    public function delete(): void
    {
        if (! unlink($this->path)) {
            throw new RuntimeException("Unable to delete file: {$this->path}");
        }
    }
}
