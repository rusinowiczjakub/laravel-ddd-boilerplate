<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

final readonly class MemberListResponse
{
    /**
     * @param MemberResponse[] $members
     */
    public function __construct(
        public array $members,
    ) {
    }

    /**
     * @return array{members: array<array{id: string, userId: string, name: string, email: string, role: string, addedAt: string}>}
     */
    public function toArray(): array
    {
        return [
            'members' => array_map(
                fn(MemberResponse $member) => $member->toArray(),
                $this->members
            ),
        ];
    }
}
