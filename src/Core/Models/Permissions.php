<?php

declare(strict_types=1);

namespace Modules\Core\Models;

enum Permissions: string
{
    case CREATE_PRODUCT = 'create_product';
    case READ_PRODUCT = 'read_product';
    case UPDATE_PRODUCT = 'update_product';
    case DELETE_PRODUCT = 'delete_product';

    public static function forRole(Roles $role): array
    {
        return match ($role) {
            Roles::VENDOR, Roles::ADMIN => [
                self::CREATE_PRODUCT,
                self::READ_PRODUCT,
                self::UPDATE_PRODUCT,
                self::DELETE_PRODUCT,
            ],
            Roles::CLIENT => [
                self::READ_PRODUCT,
            ],
        };
    }
}
