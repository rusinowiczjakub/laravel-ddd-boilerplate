import { AppBrand } from '@/components/app-brand';
import { cn } from '@/lib/utils';
import { SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { useEffect, useState } from 'react';

const navLinks: { href: string; label: string }[] = [
    // Add your navigation links here
];

export function MarketingNavbar() {
    const { waitlistMode } = usePage<SharedData>().props;
    const [isScrolled, setIsScrolled] = useState(false);
    const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setIsScrolled(window.scrollY > 24);
        };
        handleScroll();
        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    return (
        <>
            <header
                className={cn(
                    'fixed inset-x-0 top-0 z-50 transition-all duration-300',
                    isScrolled ? 'px-0 pt-0' : 'px-4 pt-4 lg:px-8'
                )}
            >
                <div
                    className={cn(
                        'mx-auto flex items-center justify-between border border-transparent bg-transparent px-4 py-2.5 backdrop-blur-md transition-all duration-300 lg:px-6',
                        isScrolled
                            ? 'max-w-none rounded-none border-b border-white/10 bg-neutral-950/90 shadow-[0_4px_60px_0_rgba(0,0,0,0.90)]'
                            : 'max-w-7xl rounded-2xl border-white/10 bg-neutral-950/80'
                    )}
                >
                    {/* Logo */}
                    <AppBrand />

                    {/* Desktop Navigation - centered */}
                    <nav className="hidden items-center gap-8 text-sm font-light text-neutral-400 lg:flex">
                        {navLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className="transition-colors hover:text-white"
                            >
                                {link.label}
                            </Link>
                        ))}
                    </nav>

                    {/* Desktop CTA Buttons */}
                    <div className="hidden items-center gap-2 md:flex">
                        {waitlistMode ? (
                            <Link
                                href="/waitlist"
                                className="inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-indigo-600 px-4 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                            >
                                Join Waitlist
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href="/login"
                                    className="inline-flex h-9 cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-white/5 px-4 text-sm font-medium text-neutral-300 transition-colors hover:bg-white/10 hover:text-white"
                                >
                                    Sign in
                                </Link>
                                <Link
                                    href="/register"
                                    className="inline-flex h-9 cursor-pointer items-center justify-center rounded-lg border border-indigo-500 bg-indigo-600 px-4 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                >
                                    Sign up
                                </Link>
                            </>
                        )}
                    </div>

                    {/* Mobile Menu Toggle */}
                    <button
                        onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
                        className="relative flex h-8 w-8 items-center justify-center rounded-full text-white lg:hidden"
                    >
                        {isMobileMenuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                    </button>
                </div>
            </header>

            {/* Mobile Navigation Panel */}
            {isMobileMenuOpen && (
                <div className="fixed inset-0 top-[72px] z-40 overflow-y-auto bg-neutral-950 lg:hidden">
                    <nav className="container mx-auto flex flex-col gap-4 px-4 py-8">
                        {navLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className="text-lg font-light text-neutral-400 transition-colors hover:text-white"
                                onClick={() => setIsMobileMenuOpen(false)}
                            >
                                {link.label}
                            </Link>
                        ))}
                        <div className="my-4 border-t border-neutral-800" />
                        <div className="flex gap-4">
                            {waitlistMode ? (
                                <Link
                                    href="/waitlist"
                                    className="inline-flex h-11 flex-1 cursor-pointer items-center justify-center rounded-lg border border-indigo-500 bg-indigo-600 px-4 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                >
                                    Join Waitlist
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href="/login"
                                        className="inline-flex h-11 flex-1 cursor-pointer items-center justify-center rounded-lg border border-white/10 bg-white/5 px-4 text-sm font-medium text-neutral-300 transition-colors hover:bg-white/10"
                                    >
                                        Sign in
                                    </Link>
                                    <Link
                                        href="/register"
                                        className="inline-flex h-11 flex-1 cursor-pointer items-center justify-center rounded-lg border border-indigo-500 bg-indigo-600 px-4 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                    >
                                        Sign up
                                    </Link>
                                </>
                            )}
                        </div>
                    </nav>
                </div>
            )}
        </>
    );
}
