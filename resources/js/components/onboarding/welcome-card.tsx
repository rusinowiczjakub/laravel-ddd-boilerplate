import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { RocketIcon } from 'lucide-react';

interface WelcomeCardProps {
    userName: string;
    onGetStarted: () => void;
    isLoading?: boolean;
}

export const WelcomeCard = ({ userName, onGetStarted, isLoading = false }: WelcomeCardProps) => {
    return (
        <Card className="mx-auto max-w-2xl border-sidebar-border bg-sidebar p-8 sm:p-12">
            <div className="flex flex-col items-center text-center">
                <div className="mb-6 rounded-full bg-primary/10 p-4">
                    <RocketIcon className="size-12 text-primary" />
                </div>

                <h1 className="mb-3 text-3xl font-bold tracking-tight sm:text-4xl">
                    Welcome, {userName}!
                </h1>

                <p className="mb-8 max-w-lg text-muted-foreground">
                    Let's get you set up. We'll create your first workspace and help you invite
                    your team. This will only take a minute.
                </p>

                <Button
                    onClick={onGetStarted}
                    disabled={isLoading}
                    size="lg"
                    className="min-w-[200px]"
                >
                    {isLoading ? 'Loading...' : 'Get Started'}
                </Button>
            </div>
        </Card>
    );
};
