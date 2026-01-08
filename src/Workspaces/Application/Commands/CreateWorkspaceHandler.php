<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Illuminate\Database\QueryException;
use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Shared\Domain\Exceptions\DomainException;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Application\Responses\WorkspaceCreatedResponse;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Models\Plan;
use Modules\Workspaces\Domain\Models\Workspace;
use Modules\Workspaces\Domain\Models\WorkspaceMember;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\Services\WorkspaceSlugGenerator;
use Modules\Workspaces\Domain\ValueObjects\Name;

#[CommandHandler(CreateWorkspaceCommand::class)]
final readonly class CreateWorkspaceHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceMemberRepository $memberRepository,
        private WorkspaceSlugGenerator $workspaceSlugGenerator,
        private EventBus $eventBus,
    ) {
    }

    public function handle(CreateWorkspaceCommand $command): WorkspaceCreatedResponse
    {
        $ownerId = Id::fromString($command->ownerId);

        $workspace = Workspace::create(
            name: Name::fromString($command->name),
            slug: $this->workspaceSlugGenerator->generate($command->name, $ownerId),
            plan: Plan::from($command->plan),
            ownerId: $ownerId,
        );

        try {
            $this->workspaceRepository->save($workspace);
        } catch (QueryException $e) {
            // Check if it's a unique constraint violation on slug
            if (str_contains($e->getMessage(), 'workspaces_owner_slug_unique')) {
                throw new DomainException(
                    'A workspace with this name already exists in your account. Please choose a different name.',
                    ['slug' => $workspace->slug()->value()]
                );
            }

            throw $e;
        }

        // Add owner as administrator member
        $ownerMember = WorkspaceMember::create(
            workspaceId: $workspace->id(),
            userId: $ownerId,
            role: WorkspaceRole::ADMINISTRATOR,
        );

        $this->memberRepository->save($ownerMember);

        $this->eventBus->dispatch(
            ...$workspace->pullEvents()
        );

        return new WorkspaceCreatedResponse(
            workspaceId: $workspace->id(),
            name: $workspace->name(),
            slug: $workspace->slug(),
            plan: $workspace->plan(),
        );
    }
}
