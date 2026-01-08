<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\AcceptInvitationCommand;
use Modules\Workspaces\Application\Commands\InviteMemberCommand;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

final class InvitationController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly WorkspaceInvitationRepository $invitationRepository,
    ) {
    }

    public function index(Request $request, string $workspaceId): JsonResponse
    {
        $invitations = $this->invitationRepository->findPendingByWorkspace(
            WorkspaceId::fromString($workspaceId)
        );

        return response()->json([
            'data' => array_map(fn($invitation) => [
                'id' => $invitation->id()->value(),
                'workspaceId' => $invitation->workspaceId()->value(),
                'email' => $invitation->email()->value,
                'role' => $invitation->role()->value,
                'token' => $invitation->token()->value,
                'status' => $invitation->status()->value,
                'invitedBy' => $invitation->invitedBy()->value(),
                'createdAt' => $invitation->createdAt()->toISOString(),
                'expiresAt' => $invitation->expiresAt()?->toISOString(),
                'acceptedAt' => $invitation->acceptedAt()?->toISOString(),
            ], $invitations),
        ]);
    }

    public function store(Request $request, string $workspaceId): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string', 'in:administrator,collaborator'],
        ]);

        $response = $this->commandBus->dispatch(
            new InviteMemberCommand(
                workspaceId: $workspaceId,
                email: $validated['email'],
                role: $validated['role'],
                invitedBy: $request->user()->id->value(),
            )
        );

        return response()->json([
            'invitation' => [
                'id' => $response->invitationId->value(),
                'workspaceId' => $workspaceId,
                'email' => $response->email->value,
                'role' => $validated['role'],
                'token' => $response->token->value,
                'status' => 'pending',
                'invitedBy' => $request->user()->id,
                'createdAt' => now()->toISOString(),
                'expiresAt' => $response->expiresAt?->toISOString(),
                'acceptedAt' => null,
            ],
        ], 201);
    }

    public function accept(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $this->commandBus->dispatch(
            new AcceptInvitationCommand(
                token: $validated['token'],
                userId: $request->user()->id->value(),
            )
        );

        return response()->json([
            'message' => 'Invitation accepted successfully',
        ]);
    }
}
