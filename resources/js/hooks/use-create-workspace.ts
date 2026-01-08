import { useState } from 'react';
import { router } from '@inertiajs/react';

interface UseCreateWorkspaceReturn {
    name: string;
    setName: (name: string) => void;
    continueToPlans: (e: React.FormEvent) => void;
    isLoading: boolean;
}

export const useCreateWorkspace = (): UseCreateWorkspaceReturn => {
    const [name, setName] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const continueToPlans = (e: React.FormEvent) => {
        e.preventDefault();
        if (!name.trim()) return;

        setIsLoading(true);
        router.visit('/onboarding/select-plan', {
            data: { name: name.trim() },
            onFinish: () => setIsLoading(false),
        });
    };

    return {
        name,
        setName,
        continueToPlans,
        isLoading,
    };
};
