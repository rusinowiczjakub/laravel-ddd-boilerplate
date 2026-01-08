import { router } from '@inertiajs/react';
import { Command, Home, Search, Settings } from 'lucide-react';
import React, { useCallback, useEffect, useState } from 'react';
import { Dialog, DialogContent, DialogTitle } from './ui/dialog';

export interface CommandItem {
    id: string;
    title: string;
    description?: string;
    icon?: React.ReactNode;
    url?: string;
    action?: () => void;
    category: 'navigation' | 'design' | 'action';
}

interface CommandPaletteProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

// Static navigation items - defined outside component to avoid recreating on each render
const staticItems: CommandItem[] = [
    {
        id: 'nav-dashboard',
        title: 'Dashboard',
        description: 'Go to dashboard',
        icon: <Home className="h-4 w-4" />,
        url: '/dashboard',
        category: 'navigation',
    },
    {
        id: 'nav-settings',
        title: 'Settings',
        description: 'Manage your account settings',
        icon: <Settings className="h-4 w-4" />,
        url: '/settings/profile',
        category: 'navigation',
    },
];

export default function CommandPalette({ open, onOpenChange }: CommandPaletteProps) {
    const [search, setSearch] = useState('');
    const [selectedIndex, setSelectedIndex] = useState(0);
    const [items, setItems] = useState<CommandItem[]>([]);
    const [isLoading, setIsLoading] = useState(false);

    // Fetch dynamic results (designs, products, etc.)
    const fetchResults = useCallback(async (query: string) => {
        if (!query.trim()) {
            setItems(staticItems);
            setIsLoading(false);
            return;
        }

        setIsLoading(true);
        try {
            const response = await fetch(`/api/search?query=${encodeURIComponent(query)}`, {
                method: 'GET',
                // @ts-expect-error i don't know why is this an error
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.textContent,
                },
                credentials: 'same-origin',
            });
            if (response.ok) {
                const data = await response.json();
                // Combine static items with dynamic results
                const filtered = staticItems.filter(
                    (item) =>
                        item.title.toLowerCase().includes(query.toLowerCase()) ||
                        item.description?.toLowerCase().includes(query.toLowerCase())
                );
                setItems([...filtered, ...data.results]);
            } else {
                // Fallback to static items filter only
                const filtered = staticItems.filter(
                    (item) =>
                        item.title.toLowerCase().includes(query.toLowerCase()) ||
                        item.description?.toLowerCase().includes(query.toLowerCase())
                );
                setItems(filtered);
            }
        } catch (error) {
            console.error('Search error:', error);
            // Fallback to static items
            const filtered = staticItems.filter(
                (item) =>
                    item.title.toLowerCase().includes(query.toLowerCase()) ||
                    item.description?.toLowerCase().includes(query.toLowerCase())
            );
            setItems(filtered);
        } finally {
            setIsLoading(false);
        }
    }, []);

    useEffect(() => {
        if (open) {
            // Initialize with static items immediately when opened
            if (search.trim() === '') {
                setItems(staticItems);
            }

            // Debounce search - wait 300ms after user stops typing
            const timeoutId = setTimeout(() => {
                fetchResults(search);
            }, 300);

            return () => clearTimeout(timeoutId);
        } else {
            setSearch('');
            setSelectedIndex(0);
            setItems([]);
        }
    }, [open, search, fetchResults]);

    // Reset selection when items change
    useEffect(() => {
        setSelectedIndex(0);
    }, [items]);

    // Handle keyboard navigation
    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setSelectedIndex((prev) => (prev + 1) % items.length);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setSelectedIndex((prev) => (prev - 1 + items.length) % items.length);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            handleSelect(items[selectedIndex]);
        }
    };

    // Handle item selection
    const handleSelect = (item: CommandItem) => {
        if (item.action) {
            item.action();
        } else if (item.url) {
            router.visit(item.url);
        }
        onOpenChange(false);
    };

    // Group items by category
    const groupedItems = items.reduce(
        (acc, item) => {
            if (!acc[item.category]) {
                acc[item.category] = [];
            }
            acc[item.category].push(item);
            return acc;
        },
        {} as Record<string, CommandItem[]>
    );

    const categoryLabels: Record<string, string> = {
        navigation: 'Navigation',
        design: 'Designs',
        action: 'Actions',
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="top-[20%] max-h-[600px] max-w-2xl translate-y-0 gap-0 p-0 [&>button]:hidden">
                <DialogTitle className="sr-only">Command Palette</DialogTitle>
                <div className="flex flex-col">
                    {/* Search Input */}
                    <div className="flex items-center border-b px-4">
                        <Search className="mr-2 h-4 w-4 shrink-0 text-slate-500" />
                        <input
                            className="flex h-14 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-slate-500 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Search for designs, navigation, or actions..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={handleKeyDown}
                            autoFocus
                        />
                        <kbd className="pointer-events-none hidden h-5 select-none items-center gap-1 rounded border bg-slate-100 px-1.5 font-mono text-[10px] font-medium opacity-100 sm:flex">
                            <span className="text-xs">ESC</span>
                        </kbd>
                    </div>

                    {/* Results */}
                    <div className="max-h-[400px] overflow-y-auto p-2">
                        {isLoading ? (
                            <div className="flex items-center justify-center py-8">
                                <div className="text-sm text-slate-500">Searching...</div>
                            </div>
                        ) : items.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-8 text-center">
                                <Command className="mb-2 h-8 w-8 text-slate-300" />
                                <p className="text-sm font-medium text-slate-900">No results found</p>
                                <p className="mt-1 text-xs text-slate-500">Try a different search term</p>
                            </div>
                        ) : (
                            Object.entries(groupedItems).map(([category, categoryItems]) => (
                                <div key={category} className="mb-2">
                                    <div className="mb-1 px-2 py-1.5 text-xs font-semibold text-slate-500">
                                        {categoryLabels[category]}
                                    </div>
                                    {categoryItems.map((item) => {
                                        const globalIndex = items.findIndex((i) => i.id === item.id);
                                        return (
                                            <button
                                                key={item.id}
                                                className={`flex w-full items-center gap-3 rounded-md px-2 py-2.5 text-left text-sm transition-colors ${
                                                    globalIndex === selectedIndex
                                                        ? 'bg-indigo-50 text-indigo-900'
                                                        : 'text-slate-900 hover:bg-slate-100'
                                                }`}
                                                onClick={() => handleSelect(item)}
                                                onMouseEnter={() => setSelectedIndex(globalIndex)}
                                            >
                                                {item.icon && (
                                                    <div
                                                        className={`flex h-8 w-8 items-center justify-center rounded-md ${
                                                            globalIndex === selectedIndex
                                                                ? 'bg-indigo-100 text-indigo-600'
                                                                : 'bg-slate-100 text-slate-600'
                                                        }`}
                                                    >
                                                        {item.icon}
                                                    </div>
                                                )}
                                                <div className="flex-1">
                                                    <div className="font-medium">{item.title}</div>
                                                    {item.description && (
                                                        <div className="text-xs text-slate-500">{item.description}</div>
                                                    )}
                                                </div>
                                                {globalIndex === selectedIndex && (
                                                    <kbd className="hidden h-5 select-none items-center gap-1 rounded border bg-white px-1.5 font-mono text-[10px] font-medium opacity-100 sm:flex">
                                                        <span className="text-xs">↵</span>
                                                    </kbd>
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>
                            ))
                        )}
                    </div>

                    {/* Footer */}
                    <div className="flex items-center justify-between border-t bg-slate-50 px-4 py-2 text-xs text-slate-500">
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-1">
                                <kbd className="flex h-5 w-5 items-center justify-center rounded border bg-white font-mono">↑</kbd>
                                <kbd className="flex h-5 w-5 items-center justify-center rounded border bg-white font-mono">↓</kbd>
                                <span className="ml-1">to navigate</span>
                            </div>
                            <div className="flex items-center gap-1">
                                <kbd className="flex h-5 items-center justify-center rounded border bg-white px-1.5 font-mono">↵</kbd>
                                <span className="ml-1">to select</span>
                            </div>
                        </div>
                        <div className="flex items-center gap-1">
                            <kbd className="flex h-5 items-center justify-center rounded border bg-white px-1.5 font-mono text-[10px]">
                                ESC
                            </kbd>
                            <span>to close</span>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
