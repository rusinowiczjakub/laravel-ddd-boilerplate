import { UserAvatar } from '@/components/user-avatar';
import { type User } from '@/types';

export function UserInfo({ user, showEmail = false }: { user: User; showEmail?: boolean }) {
    // Use first_name + last_name if available, otherwise fall back to name
    const fullName = user.first_name && user.last_name
        ? `${user.first_name} ${user.last_name}`
        : user.name;

    return (
        <>
            <UserAvatar name={fullName} avatar={user.avatar} size="md" />
            <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{fullName}</span>
                {showEmail && <span className="text-muted-foreground truncate text-xs">{user.email}</span>}
            </div>
        </>
    );
}
