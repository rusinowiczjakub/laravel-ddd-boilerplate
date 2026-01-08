import OnboardingLayout from '@/layouts/onboarding-layout';
import { useInviteTeam } from '@/hooks/use-invite-team';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { XIcon } from 'lucide-react';
import { usePage, router } from '@inertiajs/react';
import { WorkspaceRole } from '@/types/workspace';

export default function InviteTeam() {
    const { workspaceName, plan } = usePage<{ workspaceName: string; plan: string }>().props;
    const { invites, addInvite, removeInvite, updateEmail, updateRole, finishOnboarding, skip, isLoading } = useInviteTeam(workspaceName, plan);

    const goBack = () => {
        router.visit('/onboarding/select-plan', {
            data: { name: workspaceName },
        });
    };

    return (
        <OnboardingLayout
            title="Invite Team"
            showBack={!isLoading}
            onBack={goBack}
        >
            <div className="w-full max-w-md">
                <div className="rounded-xl border border-border bg-card p-8">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-2xl font-semibold text-foreground">
                            Invite your team
                        </h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Add team members to <strong>{workspaceName}</strong> workspace.
                        </p>
                    </div>

                    <div className="space-y-6">
                        {/* Invitations */}
                        <div className="space-y-3">
                            <Label className="text-sm text-foreground">
                                Team members
                            </Label>
                            {invites.map((invite, index) => (
                                <div key={index} className="flex gap-2">
                                    <Input
                                        type="email"
                                        placeholder="email@example.com"
                                        value={invite.email}
                                        onChange={(e) => updateEmail(index, e.target.value)}
                                        disabled={isLoading}
                                        className="flex-1"
                                    />
                                    <Select
                                        value={invite.role}
                                        onValueChange={(value) => updateRole(index, value as WorkspaceRole)}
                                        disabled={isLoading}
                                    >
                                        <SelectTrigger className="w-40">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value={WorkspaceRole.ADMINISTRATOR}>Admin</SelectItem>
                                            <SelectItem value={WorkspaceRole.COLLABORATOR}>Collaborator</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {invites.length > 1 && (
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => removeInvite(index)}
                                            disabled={isLoading}
                                        >
                                            <XIcon className="size-4" />
                                        </Button>
                                    )}
                                </div>
                            ))}
                        </div>

                        {/* Add more button */}
                        {invites.length < 5 && (
                            <Button
                                type="button"
                                variant="outline"
                                onClick={addInvite}
                                disabled={isLoading}
                                className="w-full"
                            >
                                Add another
                            </Button>
                        )}

                        {/* Action buttons */}
                        <div className="flex gap-3">
                            <Button
                                type="button"
                                variant="ghost"
                                onClick={skip}
                                disabled={isLoading}
                                className="flex-1"
                            >
                                Skip for now
                            </Button>
                            <Button
                                onClick={finishOnboarding}
                                disabled={isLoading || !invites.some(inv => inv.email.trim())}
                                className="flex-1"
                                size="lg"
                            >
                                {isLoading ? 'Creating...' : 'Finish'}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </OnboardingLayout>
    );
}
