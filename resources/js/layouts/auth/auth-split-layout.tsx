import { AppBrand } from '@/components/app-brand';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    title?: string;
    description?: string;
}

export default function AuthSplitLayout({ children, title, description }: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            {/* Left panel - branding */}
            <div className="relative hidden h-full flex-col overflow-hidden bg-neutral-900 p-10 text-white lg:flex">
                {/* Gradient background */}
                <div className="pointer-events-none absolute inset-0">
                    <div className="absolute inset-0 bg-gradient-to-br from-indigo-600/20 via-transparent to-purple-600/10" />
                    <div className="absolute -top-1/2 -left-1/2 h-full w-full rounded-full bg-indigo-500/10 blur-[120px]" />
                    <div className="absolute -bottom-1/2 -right-1/2 h-full w-full rounded-full bg-purple-500/10 blur-[120px]" />
                </div>

                {/* Grid pattern */}
                <div
                    className="pointer-events-none absolute inset-0 opacity-[0.02]"
                    style={{
                        backgroundImage: `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='white'%3e%3cpath d='M0 .5H31.5V32'/%3e%3c/svg%3e")`,
                    }}
                />

                {/* Logo */}
                <div className="relative z-20">
                    <AppBrand />
                </div>

                {/* Spacer */}
                <div className="flex-1" />

                {/* Bottom tagline */}
                <div className="relative z-20">
                    <p className="text-sm text-neutral-400">
                        DDD + CQRS architecture for modern applications
                    </p>
                </div>
            </div>

            {/* Right panel - form */}
            <div className="w-full bg-background lg:p-8">
                <div className="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <div className="relative z-20 flex items-center justify-center lg:hidden">
                        <AppBrand />
                    </div>
                    <div className="flex flex-col items-start gap-2 text-left sm:items-center sm:text-center">
                        <h1 className="text-xl font-medium">{title}</h1>
                        <p className="text-muted-foreground text-sm text-balance">{description}</p>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
