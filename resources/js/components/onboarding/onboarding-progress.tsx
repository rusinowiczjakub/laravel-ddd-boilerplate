import { CheckIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Step {
    number: number;
    label: string;
}

interface OnboardingProgressProps {
    currentStep: number;
    steps: Step[];
}

export const OnboardingProgress = ({ currentStep, steps }: OnboardingProgressProps) => {
    return (
        <div className="mb-12">
            <div className="mx-auto flex max-w-2xl items-center justify-center">
                {steps.map((step, index) => (
                    <div key={step.number} className="flex items-center">
                        <div className="flex flex-col items-center">
                            <div
                                className={cn(
                                    'flex size-10 items-center justify-center rounded-full border-2 font-semibold transition-colors',
                                    currentStep > step.number
                                        ? 'border-primary bg-primary text-primary-foreground'
                                        : currentStep === step.number
                                        ? 'border-primary bg-background text-primary'
                                        : 'border-muted bg-background text-muted-foreground'
                                )}
                            >
                                {currentStep > step.number ? (
                                    <CheckIcon className="size-5" />
                                ) : (
                                    step.number
                                )}
                            </div>
                            <span
                                className={cn(
                                    'mt-2 text-xs font-medium transition-colors',
                                    currentStep >= step.number
                                        ? 'text-foreground'
                                        : 'text-muted-foreground'
                                )}
                            >
                                {step.label}
                            </span>
                        </div>

                        {index < steps.length - 1 && (
                            <div
                                className={cn(
                                    'mx-4 h-0.5 w-16 transition-colors sm:w-24',
                                    currentStep > step.number
                                        ? 'bg-primary'
                                        : 'bg-muted'
                                )}
                            />
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};
