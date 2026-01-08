<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Storage;

use DateTimeInterface;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage as StorageFacade;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Modules\Shared\Domain\Storage\Exceptions\StorageException;
use Modules\Shared\Domain\Storage\Exceptions\StorageNotFound;
use Modules\Shared\Domain\Storage\File;
use Modules\Shared\Domain\Storage\Storage;
use Throwable;

use function basename;
use function fclose;
use function fopen;
use function is_resource;
use function ltrim;
use function trim;

final class IlluminateStorage implements Storage
{
    public function __construct(
        private readonly string $disk = 'local',
        private readonly ?FilesystemFactory $factory = null,
    ) {
    }

    public function withDisk(string $disk): self
    {
        return new self($disk, $this->factory);
    }

    /**
     * @throws StorageException
     */
    public function put(string $path, string $contents, array $options = []): string
    {
        try {
            $this->fs()->put($this->normalize($path), $contents, $options);

            return $this->normalize($path);
        } catch (UnableToWriteFile | FilesystemException $e) {
            throw new StorageException("Unable to put file [$path] on disk [{$this->disk}]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function putStream(string $path, $stream, array $options = []): string
    {
        if (! is_resource($stream)) {
            throw new StorageException('putStream expects a valid resource stream.');
        }
        try {
            $this->fs()->put($this->normalize($path), $stream, $options);

            return $this->normalize($path);
        } catch (UnableToWriteFile | FilesystemException $e) {
            throw new StorageException("Unable to put stream [$path] on disk [{$this->disk}]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function putFile(string $directory, File $file, ?string $filename = null, array $options = []): string
    {
        $dir = trim($directory, '/');
        $name = $filename ?? ($file->originalName() ?? basename($file->path()));
        $target = $this->normalize(($dir ? $dir . '/' : '') . $name);

        $stream = @fopen($file->path(), 'rb');
        if (! is_resource($stream)) {
            throw new StorageException("Cannot open source file stream: {$file->path()}");
        }

        try {
            $this->fs()->put($target, $stream, $options);

            return $target;
        } catch (UnableToWriteFile | FilesystemException $e) {
            throw new StorageException("Unable to store file as [$target] on disk [{$this->disk}]: {$e->getMessage()}", previous: $e);
        } finally {
            is_resource($stream) && fclose($stream);
        }
    }

    /**
     * @throws StorageNotFound
     * @throws StorageException
     */
    public function get(string $path): string
    {
        try {
            return $this->fs()->get($this->normalize($path));
        } catch (UnableToReadFile $e) {
            throw new StorageNotFound("File not found or unreadable: {$path}");
        } catch (FilesystemException $e) {
            throw new StorageException("Unable to read file [$path]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageNotFound
     * @throws StorageException
     */
    public function readStream(string $path)
    {
        try {
            $stream = $this->fs()->readStream($this->normalize($path));
            if (! is_resource($stream)) {
                throw new StorageException("Unable to open read stream for: {$path}");
            }

            return $stream;
        } catch (UnableToReadFile $e) {
            throw new StorageNotFound("File not found or unreadable: {$path}");
        } catch (FilesystemException $e) {
            throw new StorageException("Unable to open read stream [$path]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function delete(string $path): void
    {
        try {
            $this->fs()->delete($this->normalize($path));
        } catch (UnableToDeleteFile $e) {
        } catch (FilesystemException $e) {
            throw new StorageException("Unable to delete [$path]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function deleteDirectory(string $directory): void
    {
        try {
            $this->fs()->deleteDirectory($this->normalize($directory));
        } catch (UnableToDeleteDirectory | FilesystemException $e) {
            throw new StorageException("Unable to delete directory [$directory]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function exists(string $path): bool
    {
        try {
            return $this->fs()->exists($this->normalize($path));
        } catch (UnableToCheckFileExistence | FilesystemException $e) {
            throw new StorageException("Unable to check existence for [$path]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function size(string $path): int
    {
        try {
            return $this->fs()->size($this->normalize($path));
        } catch (UnableToRetrieveMetadata | FilesystemException $e) {
            throw new StorageException("Unable to retrieve size for [$path]: {$e->getMessage()}", previous: $e);
        }
    }

    public function mimeType(string $path): ?string
    {
        try {
            return $this->fs()->mimeType($this->normalize($path));
        } catch (UnableToRetrieveMetadata | FilesystemException $e) {
            return null;
        }
    }

    public function url(string $path): ?string
    {
        try {
            $fs = $this->fs();

            return $fs->url($this->normalize($path));
        } catch (Throwable $e) {
            return null;
        }
    }

    public function temporaryUrl(string $path, DateTimeInterface $expires, array $options = []): ?string
    {
        try {
            $fs = $this->fs();

            return $fs->temporaryUrl($this->normalize($path), $expires, $options);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @throws StorageException
     */
    public function copy(string $from, string $to): void
    {
        try {
            $this->fs()->copy($this->normalize($from), $this->normalize($to));
        } catch (UnableToCopyFile | FilesystemException $e) {
            throw new StorageException("Unable to copy [$from] to [$to]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function move(string $from, string $to): void
    {
        try {
            $this->fs()->move($this->normalize($from), $this->normalize($to));
        } catch (UnableToMoveFile | FilesystemException $e) {
            throw new StorageException("Unable to move [$from] to [$to]: {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * @throws StorageException
     */
    public function makeDirectory(string $directory, array $options = []): void
    {
        try {
            $fs = $this->fs();
            $fs->makeDirectory($this->normalize($directory));
            if (! empty($options['visibility'])) {
                $fs->setVisibility($this->normalize($directory), $options['visibility']);
            }
        } catch (FilesystemException $e) {
            throw new StorageException("Unable to make directory [$directory]: {$e->getMessage()}", previous: $e);
        }
    }

    public function path(string $path): ?string
    {
        try {
            $fs = $this->fs();

            return $fs->path($this->normalize($path));
        } catch (Throwable $e) {
        }

        return null;
    }

    /**
     * @throws StorageNotFound
     * @throws StorageException
     */
    public function getFile(string $path): File
    {
        $normalizedPath = $this->normalize($path);

        if (! $this->exists($path)) {
            throw new StorageNotFound("File not found: {$path}");
        }

        try {
            $size = $this->size($path);
            $mimeType = $this->mimeType($path);
            $absolutePath = $this->path($path);

            return new File(
                path: $absolutePath ?: $normalizedPath,
                mimeType: $mimeType,
                size: $size,
                originalName: basename($normalizedPath)
            );
        } catch (Throwable $e) {
            throw new StorageException("Unable to get file info for [$path]: {$e->getMessage()}", previous: $e);
        }
    }

    private function fs(): Filesystem
    {
        return $this->factory
            ? $this->factory->disk($this->disk)
            : StorageFacade::disk($this->disk);
    }

    private function normalize(string $path): string
    {
        return ltrim($path, '/');
    }
}
