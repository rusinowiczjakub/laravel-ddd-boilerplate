<?php

declare(strict_types=1);

use Modules\Shared\Domain\Storage\File;

it('creates file with all properties', function () {
    $file = new File('/path/to/file.jpg', 'image/jpeg', 1024, 'original.jpg');

    expect($file->path())->toBe('/path/to/file.jpg');
    expect($file->mimeType())->toBe('image/jpeg');
    expect($file->size())->toBe(1024);
    expect($file->originalName())->toBe('original.jpg');
});

it('gets extension from original name', function () {
    $file = new File('/tmp/abc123', 'image/jpeg', 1024, 'photo.JPG');

    expect($file->extensionOr())->toBe('jpg');
});

it('gets extension from path when no original name', function () {
    $file = new File('/path/to/file.PDF');

    expect($file->extensionOr())->toBe('pdf');
});

it('returns fallback extension when no extension found', function () {
    $file = new File('/path/to/file');

    expect($file->extensionOr('bin'))->toBe('bin');
    expect($file->extensionOr('txt'))->toBe('txt');
});
