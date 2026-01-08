<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Storage;

use DateTimeInterface;

interface Storage
{
    public function withDisk(string $disk): self;

    public function put(string $path, string $contents, array $options = []): string;

    public function putStream(string $path, $stream, array $options = []): string;

    public function putFile(string $directory, File $file, ?string $filename = null, array $options = []): string;

    public function get(string $path): string;

    public function readStream(string $path);

    public function delete(string $path): void;

    public function deleteDirectory(string $directory): void;

    public function exists(string $path): bool;

    public function size(string $path): int;

    public function mimeType(string $path): ?string;

    public function url(string $path): ?string;

    public function temporaryUrl(string $path, DateTimeInterface $expires, array $options = []): ?string;

    public function copy(string $from, string $to): void;

    public function move(string $from, string $to): void;

    public function makeDirectory(string $directory, array $options = []): void;

    public function path(string $path): ?string;

    public function getFile(string $path): File;
}
