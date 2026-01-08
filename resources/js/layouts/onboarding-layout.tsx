import { Head, router } from '@inertiajs/react';
import { ArrowLeftIcon, XIcon } from 'lucide-react';
import { type ReactNode } from 'react';
import { FeedbackButton } from '@/components/feedback/feedback-button';

interface OnboardingLayoutProps {
    children: ReactNode;
    title?: string;
    onClose?: () => void;
    showClose?: boolean;
    onBack?: () => void;
    showBack?: boolean;
}

export default function OnboardingLayout({
    children,
    title = 'Onboarding',
    onClose,
    showClose = true,
    onBack,
    showBack = false,
}: OnboardingLayoutProps) {
    const handleClose = () => {
        if (onClose) {
            onClose();
        } else {
            router.visit('/dashboard');
        }
    };

    return (
        <>
            <Head title={title} />
            <div className="fixed inset-0 z-50 bg-background">
                {/* Navigation buttons */}
                <div className="absolute left-6 top-6 flex items-center gap-2">
                    {showClose && (
                        <button
                            onClick={handleClose}
                            className="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        >
                            <XIcon className="size-5" />
                        </button>
                    )}
                    {showBack && (
                        <button
                            onClick={onBack}
                            className="rounded-md p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        >
                            <ArrowLeftIcon className="size-5" />
                        </button>
                    )}
                </div>

                {/* Content */}
                <div className="h-full overflow-y-auto">
                    <div className="flex min-h-full items-start md:items-center justify-center p-6 pt-16">
                        <div className="flex w-full justify-center py-6">
                            {children}
                        </div>
                    </div>
                </div>
            </div>
            <FeedbackButton />
        </>
    );
}
