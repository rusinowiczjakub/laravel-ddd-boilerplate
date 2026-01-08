import OnboardingLayout from '@/layouts/onboarding-layout';
import { useCreateWorkspace } from '@/hooks/use-create-workspace';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function CreateWorkspace() {
    const { name, setName, continueToPlans, isLoading } = useCreateWorkspace();

    return (
        <OnboardingLayout title="Create Workspace">
            <div className="w-full max-w-md">
                <div className="rounded-xl border border-border bg-card p-8">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-2xl font-semibold text-foreground">
                            Create workspace
                        </h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Let's start by naming your workspace.
                        </p>
                    </div>

                    <form onSubmit={continueToPlans} className="space-y-6">
                        {/* Workspace name */}
                        <div className="space-y-2">
                            <Label htmlFor="workspace-name" className="text-sm text-foreground">
                                Workspace name
                            </Label>
                            <Input
                                id="workspace-name"
                                type="text"
                                placeholder="Enter name"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                disabled={isLoading}
                                required
                                autoFocus
                            />
                        </div>

                        {/* Submit button */}
                        <Button
                            type="submit"
                            disabled={isLoading || !name.trim()}
                            className="w-full"
                            size="lg"
                        >
                            {isLoading ? 'Loading...' : 'Continue'}
                        </Button>
                    </form>
                </div>
            </div>
        </OnboardingLayout>
    );
}
