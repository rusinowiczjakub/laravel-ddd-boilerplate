import { AppBrand } from '@/components/app-brand';
import MarketingLayout from '@/layouts/marketing-layout';
import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, Boxes, CreditCard, Database, Layers, Lock, Users } from 'lucide-react';
import { FormEventHandler } from 'react';

function WaitlistForm() {
    const { data, setData, post, processing, errors, wasSuccessful, reset } = useForm({
        email: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/waitlist', {
            onSuccess: () => reset(),
        });
    };

    if (wasSuccessful) {
        return (
            <div className="text-center">
                <div className="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-500/10">
                    <svg className="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 className="mb-2 text-xl font-medium text-white">You're on the list!</h3>
                <p className="text-neutral-400">We'll notify you when we're ready for you.</p>
            </div>
        );
    }

    return (
        <form onSubmit={submit} className="mx-auto w-full max-w-md">
            <div className="flex flex-col gap-3 sm:flex-row">
                <div className="flex-1">
                    <input
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        placeholder="Enter your email"
                        className="h-12 w-full rounded-lg border border-neutral-700 bg-neutral-800 px-4 text-white placeholder:text-neutral-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        required
                    />
                    {errors.email && (
                        <p className="mt-1 text-sm text-red-400">{errors.email}</p>
                    )}
                </div>
                <button
                    type="submit"
                    disabled={processing}
                    className="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-indigo-500 bg-indigo-600 px-6 text-sm font-medium text-white transition-colors hover:bg-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {processing ? 'Joining...' : 'Join Waitlist'}
                    <ArrowRight className="h-4 w-4" />
                </button>
            </div>
        </form>
    );
}

function FeatureCard({ icon: Icon, title, description }: { icon: typeof Layers; title: string; description: string }) {
    return (
        <div className="rounded-xl border border-neutral-800 bg-neutral-900/50 p-6">
            <div className="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10">
                <Icon className="h-5 w-5 text-indigo-400" />
            </div>
            <h3 className="mb-2 font-medium text-white">{title}</h3>
            <p className="text-sm text-neutral-400">{description}</p>
        </div>
    );
}

export default function Waitlist() {
    const features = [
        {
            icon: Layers,
            title: 'DDD Architecture',
            description: 'Clean domain-driven design with bounded contexts and proper separation of concerns.',
        },
        {
            icon: Boxes,
            title: 'CQRS Pattern',
            description: 'Command Query Responsibility Segregation for scalable and maintainable code.',
        },
        {
            icon: Users,
            title: 'Multi-Tenancy',
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
            icon: Database,
            title: 'Modern Stack',
            description: 'Laravel 12, React 19, TypeScript, Inertia.js, and Tailwind CSS.',
        },
    ];

    return (
        <MarketingLayout>
            <Head title="Join the Waitlist" />

            {/* Hero Section */}
            <section className="relative overflow-hidden bg-[#09090b] pt-32 pb-20 lg:pt-40 lg:pb-32">
                {/* Background gradient */}
                <div
                    className="absolute inset-0"
                    style={{
                        background: 'radial-gradient(ellipse 60% 40% at 50% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 60%)',
                    }}
                />

                <div className="container relative z-10 mx-auto px-4 lg:px-8">
                    <div className="mx-auto max-w-3xl text-center">
                        {/* Badge */}
                        <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-500/20 bg-indigo-500/10 px-4 py-1.5">
                            <span className="relative flex h-2 w-2">
                                <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-75"></span>
                                <span className="relative inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                            </span>
                            <span className="text-sm font-medium text-indigo-400">Coming Soon</span>
                        </div>

                        {/* Logo */}
                        <div className="mb-8 flex justify-center">
                            <AppBrand className="scale-150" />
                        </div>

                        {/* Headline */}
                        <h1 className="mb-6 text-4xl font-medium tracking-tight text-balance text-white md:text-5xl lg:text-6xl">
                            Be the first to build
                            <br />
                            with clean architecture
                        </h1>

                        {/* Subheading */}
                        <p className="mx-auto mb-10 max-w-2xl text-lg text-neutral-400">
                            We're launching soon. Join the waitlist to get early access and start building your next project with DDD + CQRS.
                        </p>

                        {/* Waitlist Form */}
                        <WaitlistForm />

                        {/* Social proof */}
                        <p className="mt-6 text-sm text-neutral-500">
                            Join developers already on the waitlist
                        </p>
                    </div>
                </div>
            </section>

            {/* Features Preview */}
            <section className="border-t border-white/5 bg-[#09090b] py-20 lg:py-32">
                <div className="container mx-auto px-4 lg:px-8">
                    <div className="mx-auto mb-16 max-w-2xl text-center">
                        <h2 className="mb-4 text-3xl font-medium tracking-[-0.02em] text-balance text-white md:text-4xl">
                            What's included
                        </h2>
                        <p className="text-lg font-light text-neutral-400">
                            A production-ready foundation for building modern applications.
                        </p>
                    </div>

                    <div className="mx-auto grid max-w-5xl gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {features.map((feature) => (
                            <FeatureCard key={feature.title} {...feature} />
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA Section */}
            <section className="border-t border-white/5 bg-[#09090b] py-20 lg:py-32">
                <div className="container mx-auto px-4 lg:px-8">
                    <div className="mx-auto max-w-2xl text-center">
                        <h2 className="mb-4 text-3xl font-medium tracking-[-0.02em] text-balance text-white md:text-4xl">
                            Don't miss the launch
                        </h2>
                        <p className="mb-8 text-lg font-light text-neutral-400">
                            Get notified when we launch. Early access members get exclusive benefits.
                        </p>
                        <WaitlistForm />
                    </div>
                </div>
            </section>
        </MarketingLayout>
    );
}
