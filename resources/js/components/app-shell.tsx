import CommandPalette from '@/components/command-palette';
import { SidebarProvider } from '@/components/ui/sidebar';
import { CommandPaletteProvider, useCommandPalette } from '@/contexts/command-palette-context';
import { useState } from 'react';

interface AppShellProps {
    children: React.ReactNode;
    variant?: 'header' | 'sidebar';
}

function AppShellContent({ children, variant = 'header' }: AppShellProps) {
    const [isOpen, setIsOpen] = useState(() => (typeof window !== 'undefined' ? localStorage.getItem('sidebar') !== 'false' : true));
    const { open: commandPaletteOpen, setOpen: setCommandPaletteOpen } = useCommandPalette();

    const handleSidebarChange = (open: boolean) => {
        setIsOpen(open);

        if (typeof window !== 'undefined') {
            localStorage.setItem('sidebar', String(open));
        }
    };

    if (variant === 'header') {
        return (
            <div className="flex min-h-screen w-full flex-col">
                <CommandPalette open={commandPaletteOpen} onOpenChange={setCommandPaletteOpen} />
                {children}
            </div>
        );
    }

    return (
        <SidebarProvider defaultOpen={isOpen} open={isOpen} onOpenChange={handleSidebarChange}>
            <CommandPalette open={commandPaletteOpen} onOpenChange={setCommandPaletteOpen} />
            {children}
        </SidebarProvider>
    );
}

export function AppShell({ children, variant = 'header' }: AppShellProps) {
    return (
        <CommandPaletteProvider>
            <AppShellContent variant={variant}>{children}</AppShellContent>
        </CommandPaletteProvider>
    );
}
