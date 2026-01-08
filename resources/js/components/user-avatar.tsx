import { cn } from '@/lib/utils';

interface UserAvatarProps {
    name: string;
    avatar?: string | null;
    size?: 'sm' | 'md' | 'lg';
    className?: string;
}

export function UserAvatar({
    name,
    avatar,
    size = 'md',
    className,
}: UserAvatarProps) {
    const getInitials = (name: string): string => {
        return name
            .split(' ')
            .filter(word => word.length > 0)
            .map((word) => word[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    const sizeClasses = {
        sm: 'size-6 text-xs',
        md: 'size-8 text-sm',
        lg: 'size-12 text-base',
    };

    const avatarUrl = avatar && avatar.trim() !== '' ? `/storage/${avatar}` : null;

    if (avatarUrl) {
        return (
            <img
                src={avatarUrl}
                alt={name}
                className={cn('rounded-lg object-cover', sizeClasses[size], className)}
            />
        );
    }

    return (
        <div
            className={cn(
                'flex items-center justify-center rounded-lg font-semibold bg-muted text-muted-foreground',
                sizeClasses[size],
                className
            )}
        >
            {getInitials(name)}
        </div>
    );
}
