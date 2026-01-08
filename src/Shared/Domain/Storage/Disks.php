<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Storage;

enum Disks: string
{
    case SOURCES = 'source';
    case MOCKUPS = 'mockup';
    case DESIGNS = 'design';
    case PREVIEWS = 'preview';
}
