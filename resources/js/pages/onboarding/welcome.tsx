import OnboardingLayout from '@/layouts/onboarding-layout';
import { WelcomeCard } from '@/components/onboarding/welcome-card';
import { OnboardingProgress } from '@/components/onboarding/onboarding-progress';
import { router, usePage } from '@inertiajs/react';
import type { Auth } from '@/types';

const steps = [
    { number: 1, label: 'Welcome' },
    { number: 2, label: 'Create Workspace' },
    { number: 3, label: 'Invite Team' },
];

export default function Welcome() {
    const { auth } = usePage<{ auth: Auth }>().props;

    const handleGetStarted = () => {
        router.visit('/onboarding/create-workspace');
    };

    return (
        <OnboardingLayout title="Welcome">
            <OnboardingProgress currentStep={1} steps={steps} />
            <WelcomeCard userName={auth.user.name} onGetStarted={handleGetStarted} />
        </OnboardingLayout>
    );
}
