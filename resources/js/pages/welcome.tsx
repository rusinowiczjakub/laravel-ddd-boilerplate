import { FadeIn } from '@/components/motion';
import MarketingLayout from '@/layouts/marketing-layout';
import { SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, CreditCard, Lock, Shield, Users, Zap } from 'lucide-react';

const features = [
    {
        icon: Users,
        title: 'Multi-tenant Workspaces',
        description: 'Built-in workspace management with team members, roles, and invitations.',
    },
    {
        icon: Lock,
        title: 'Authentication',
        description: 'Complete auth system with email verification, 2FA, and password recovery.',
    },
    {
        icon: CreditCard,
        title: 'Stripe Billing',
        description: 'Subscription management, usage tracking, and billing portal integration.',
    },
    {
        icon: Zap,
        title: 'CQRS Architecture',
        description: 'Clean architecture with Command/Query separation and domain events.',
    },
    {
        icon: Shield,
        title: 'Security First',
        description: 'CSRF protection, rate limiting, and secure session management.',
    },
];

function HeroSection({ waitlistMode }: { waitlistMode: boolean }) {
    return (
        <section className="relative overflow-hidden bg-[#09090b] pt-32 pb-20 lg:pt-40 lg:pb-32">
            <div
                className="pointer-events-none absolute inset-0"
                style={{
                    background: 'radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99, 102, 241, 0.15) 0%, transparent 50%)',
                }}
            />

            <div className="relative z-10 mx-auto max-w-7xl px-4 lg:px-8">
                <div className="mx-auto max-w-3xl text-center">
                    <FadeIn>
                        <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 px-4 py-1.5">
                            <span className="text-sm font-medium text-indigo-400">Laravel + React + Inertia</span>
                        </div>
                    </FadeIn>

                    <FadeIn delay={0.1}>
                        <h1 className="mb-6 text-5xl font-medium tracking-tight text-balance text-white md:text-6xl lg:text-7xl">
                            DDD + CQRS
                            <br />
                            <span className="text-indigo-400">Laravel Boilerplate</span>
                        </h1>
                    </FadeIn>

                    <FadeIn delay={0.2}>
                        <p className="mx-auto mb-10 max-w-2xl text-lg text-neutral-400">
                            A production-ready foundation with Domain-Driven Design and CQRS architecture.
                            Multi-tenancy, authentication, billing, and clean code out of the box.
                        </p>
                    </FadeIn>

                    <FadeIn delay={0.3}>
                        <div className="flex flex-col items-center justify-center gap-4 sm:flex-row">
                            {waitlistMode ? (
                                <Link
                                    href="/waitlist"
                                    className="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-indigo-600 px-6 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                >
                                    Join Waitlist
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <Link
                                    href="/register"
                                    className="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-indigo-600 px-6 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                >
                                    Get Started
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            )}
                            <Link
                                href="/login"
                                className="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-white/10 bg-white/5 px-6 text-sm font-medium text-white transition-colors hover:bg-white/10"
                            >
                                Sign In
                            </Link>
                        </div>
                    </FadeIn>
                </div>
            </div>
        </section>
    );
}

function FeaturesSection() {
    return (
        <section className="border-t border-white/5 bg-[#09090b] py-20 lg:py-32">
            <div className="mx-auto max-w-7xl px-4 lg:px-8">
                <div className="mx-auto mb-16 max-w-2xl text-center">
                    <FadeIn>
                        <h2 className="mb-4 text-3xl font-medium tracking-tight text-white md:text-4xl">
                            Everything you need to start
                        </h2>
                    </FadeIn>
                    <FadeIn delay={0.1}>
                        <p className="text-lg text-neutral-400">
                            Skip the boilerplate setup and focus on building your product.
                        </p>
                    </FadeIn>
                </div>

                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {features.map((feature, index) => (
                        <FadeIn key={feature.title} delay={index * 0.1}>
                            <div className="rounded-xl border border-white/5 bg-white/[0.02] p-6 transition-colors hover:bg-white/[0.04]">
                                <div className="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10">
                                    <feature.icon className="h-5 w-5 text-indigo-400" />
                                </div>
                                <h3 className="mb-2 font-medium text-white">{feature.title}</h3>
                                <p className="text-sm text-neutral-400">{feature.description}</p>
                            </div>
                        </FadeIn>
                    ))}
                </div>
            </div>
        </section>
    );
}

function TechStackSection() {
    const stack = [
        { name: 'Laravel 12', description: 'PHP Framework' },
        { name: 'React 19', description: 'UI Library' },
        { name: 'Inertia.js', description: 'SPA Bridge' },
        { name: 'TypeScript', description: 'Type Safety' },
        { name: 'Tailwind CSS', description: 'Styling' },
        { name: 'PostgreSQL', description: 'Database' },
    ];

    return (
        <section className="border-t border-white/5 bg-[#09090b] py-20 lg:py-32">
            <div className="mx-auto max-w-7xl px-4 lg:px-8">
                <div className="mx-auto mb-12 max-w-2xl text-center">
                    <FadeIn>
                        <h2 className="mb-4 text-3xl font-medium tracking-tight text-white md:text-4xl">
                            Modern Tech Stack
                        </h2>
                    </FadeIn>
                    <FadeIn delay={0.1}>
                        <p className="text-lg text-neutral-400">
                            Built with the best tools for developer experience and performance.
                        </p>
                    </FadeIn>
                </div>

                <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    {stack.map((tech, index) => (
                        <FadeIn key={tech.name} delay={index * 0.05}>
                            <div className="rounded-lg border border-white/5 bg-white/[0.02] p-4 text-center">
                                <div className="font-medium text-white">{tech.name}</div>
                                <div className="text-xs text-neutral-500">{tech.description}</div>
                            </div>
                        </FadeIn>
                    ))}
                </div>
            </div>
        </section>
    );
}

function CTASection({ waitlistMode }: { waitlistMode: boolean }) {
    return (
        <section className="relative overflow-hidden border-t border-white/5 bg-[#09090b] py-20 lg:py-32">
            <div
                className="pointer-events-none absolute inset-0"
                style={{
                    background: 'radial-gradient(ellipse 50% 50% at 50% 50%, rgba(99, 102, 241, 0.1) 0%, transparent 70%)',
                }}
            />

            <div className="relative z-10 mx-auto max-w-7xl px-4 lg:px-8">
                <div className="mx-auto max-w-2xl text-center">
                    <FadeIn>
                        <h2 className="mb-4 text-3xl font-medium tracking-tight text-white md:text-4xl">
                            Ready to build?
                        </h2>
                    </FadeIn>
                    <FadeIn delay={0.1}>
                        <p className="mb-8 text-lg text-neutral-400">
                            Start building your application with clean architecture today.
                        </p>
                    </FadeIn>
                    <FadeIn delay={0.2}>
                        <div className="flex flex-col items-center justify-center gap-4 sm:flex-row">
                            {waitlistMode ? (
                                <Link
                                    href="/waitlist"
                                    className="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-indigo-600 px-6 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                >
                                    Join Waitlist
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <Link
                                    href="/register"
                                    className="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-indigo-600 px-6 text-sm font-medium text-white transition-colors hover:bg-indigo-500"
                                >
                                    Get Started
                                    <ArrowRight className="h-4 w-4" />
                                </Link>
                            )}
                        </div>
                    </FadeIn>
                </div>
            </div>
        </section>
    );
}

export default function Welcome() {
    const { waitlistMode } = usePage<SharedData>().props;

    return (
        <MarketingLayout>
            <Head title="DDD + CQRS Laravel Boilerplate" />
            <HeroSection waitlistMode={waitlistMode} />
            <FeaturesSection />
            <TechStackSection />
            <CTASection waitlistMode={waitlistMode} />
        </MarketingLayout>
    );
}
