import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { Layers } from 'lucide-react';

interface LogoProps {
    className?: string;
}

export function AppBrand({ className }: LogoProps) {
    return (
        <Link href="/" aria-label="Home" className={className}>
            <div className="group flex items-center gap-2">
                <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 transition-colors group-hover:bg-indigo-500">
                    <Layers className="h-4 w-4 text-white" />
                </div>
                <span className="text-lg font-semibold text-white transition-colors group-hover:text-indigo-400">
                    Boilerplate
                </span>
            </div>
        </Link>
    );
}

export function AppIcon({ className }: { className?: string }) {
    return <Layers className={cn('h-5 w-5', className)} />;
}
