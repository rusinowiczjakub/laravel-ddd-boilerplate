<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Enums;

enum WorkspaceRole: string
{
    case ADMINISTRATOR = 'administrator';
    case COLLABORATOR = 'collaborator';

    public function isAdministrator(): bool
    {
        return $this === self::ADMINISTRATOR;
    }

    public function isCollaborator(): bool
    {
        return $this === self::COLLABORATOR;
    }

    /**
     * Permissions for each role
     */
    public function can(string $permission): bool
    {
        return match ($this) {
            self::ADMINISTRATOR => true, // Admin can do everything
            self::COLLABORATOR => in_array($permission, [
                'view_workspace',
                'create_rules',
                'edit_rules',
                'delete_rules',
                'create_templates',
                'edit_templates',
                'delete_templates',
                'view_events',
                'view_logs',
            ]),
        };
    }

    /**
     * What collaborators CANNOT do
     */
    public function cannot(string $permission): bool
    {
        return !$this->can($permission);
    }
}
