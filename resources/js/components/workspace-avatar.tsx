import { cn } from '@/lib/utils';

interface WorkspaceAvatarProps {
    name: string;
    avatar?: string | null;
    size?: 'sm' | 'md' | 'lg';
    className?: string;
    variant?: 'sidebar' | 'default';
}

export function WorkspaceAvatar({
    name,
    avatar,
    size = 'md',
    className,
    variant = 'sidebar'
}: WorkspaceAvatarProps) {
    const getInitials = (name: string): string => {
        return name
            .split(' ')
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

    const variantClasses = {
        sidebar: 'bg-muted text-muted-foreground',
        default: 'bg-muted text-muted-foreground',
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
                'flex items-center justify-center rounded-lg font-semibold',
                sizeClasses[size],
                variantClasses[variant],
                className
            )}
        >
            {getInitials(name)}
        </div>
    );
}
