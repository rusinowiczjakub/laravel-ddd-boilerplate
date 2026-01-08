import { useCallback, useEffect, useState } from 'react';

export type Appearance = 'light' | 'dark' | 'system';

const prefersDark = () => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const applyTheme = (appearance: Appearance) => {
    const isDark = appearance === 'dark' || (appearance === 'system' && prefersDark());

    document.documentElement.classList.toggle('dark', isDark);
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const STORAGE_KEY = 'theme';

const handleSystemThemeChange = () => {
    const currentTheme = localStorage.getItem(STORAGE_KEY) as Appearance;
    applyTheme(currentTheme || 'system');
};

export function initializeTheme() {
    const savedTheme = (localStorage.getItem(STORAGE_KEY) as Appearance) || 'system';

    applyTheme(savedTheme);

    // Add the event listener for system theme changes...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance() {
    const [appearance, setAppearance] = useState<Appearance>(() => {
        if (typeof window === 'undefined') return 'system';
        return (localStorage.getItem(STORAGE_KEY) as Appearance) || 'system';
    });

    const updateAppearance = useCallback((mode: Appearance) => {
        setAppearance(mode);
        localStorage.setItem(STORAGE_KEY, mode);
        setCookie(STORAGE_KEY, mode);
        applyTheme(mode);
    }, []);

    useEffect(() => {
        // Apply theme on mount and listen for system theme changes
        applyTheme(appearance);

        const mq = mediaQuery();
        mq?.addEventListener('change', handleSystemThemeChange);

        return () => mq?.removeEventListener('change', handleSystemThemeChange);
    }, [appearance]);

    return { appearance, updateAppearance } as const;
}
