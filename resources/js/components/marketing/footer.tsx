import { AppBrand } from '@/components/app-brand';
import { Github } from 'lucide-react';

export function MarketingFooter() {
    return (
        <footer className="border-t border-white/5 bg-[#09090b]">
            <div className="container mx-auto px-4 py-12 lg:px-8 lg:py-16">
                <div className="flex flex-col items-center justify-between gap-8 md:flex-row">
                    {/* Brand */}
                    <div className="text-center md:text-left">
                        <AppBrand />
                        <p className="mt-4 max-w-md text-sm text-neutral-400">
                            A production-ready foundation with DDD + CQRS architecture for building modern applications.
                        </p>
                    </div>

                    {/* Social Links */}
                    <div className="flex gap-4">
                        <a
                            href="https://github.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-neutral-500 transition-colors hover:text-white"
                        >
                            <Github className="h-5 w-5" />
                        </a>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="mt-12 border-t border-white/10 pt-8">
                    <p className="text-center text-sm text-neutral-500">
                        &copy; {new Date().getFullYear()} Your Company. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    );
}
