import React, { createContext, useContext, useEffect, useState } from 'react';

interface CommandPaletteContextType {
    open: boolean;
    setOpen: (open: boolean) => void;
}

const CommandPaletteContext = createContext<CommandPaletteContextType | undefined>(undefined);

export function CommandPaletteProvider({ children }: { children: React.ReactNode }) {
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const down = (e: KeyboardEvent) => {
            if (e.key === 'k' && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                console.log('Command palette toggled:', !open);
                setOpen((prevOpen) => !prevOpen);
            }
        };

        document.addEventListener('keydown', down);
        return () => document.removeEventListener('keydown', down);
    });

    return <CommandPaletteContext.Provider value={{ open, setOpen }}>{children}</CommandPaletteContext.Provider>;
}

export function useCommandPalette() {
    const context = useContext(CommandPaletteContext);
    if (context === undefined) {
        throw new Error('useCommandPalette must be used within a CommandPaletteProvider');
    }
    return context;
}
