import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert } from '@/components/ui/alert';
import { Select } from '@/components/ui/select';
import { UsersIcon, AlertCircle, PlusIcon, XIcon } from 'lucide-react';
import { WorkspaceRole } from '@/types/workspace';
import type { FormEvent } from 'react';

interface InviteEntry {
    email: string;
    role: WorkspaceRole;
}

interface InviteTeamFormProps {
    invites: InviteEntry[];
    onAddInvite: () => void;
    onRemoveInvite: (index: number) => void;
    onEmailChange: (index: number, email: string) => void;
    onRoleChange: (index: number, role: WorkspaceRole) => void;
    onSubmit: (e: FormEvent) => void;
    onSkip: () => void;
    isLoading?: boolean;
    error?: string | null;
}

export const InviteTeamForm = ({
    invites,
    onAddInvite,
    onRemoveInvite,
    onEmailChange,
    onRoleChange,
    onSubmit,
    onSkip,
    isLoading = false,
    error = null,
}: InviteTeamFormProps) => {
    return (
        <Card className="mx-auto max-w-2xl border-sidebar-border bg-sidebar p-8">
            <div className="mb-6 flex items-center gap-3">
                <div className="rounded-full bg-primary/10 p-3">
                    <UsersIcon className="size-6 text-primary" />
                </div>
                <div>
                    <h2 className="text-2xl font-bold">Invite your team</h2>
                    <p className="text-sm text-muted-foreground">
                        Collaborate with your team members (optional)
                    </p>
                </div>
            </div>

            {error && (
                <Alert variant="destructive" className="mb-6">
                    <AlertCircle className="size-4" />
                    <div className="ml-2">{error}</div>
                </Alert>
            )}

            <form onSubmit={onSubmit} className="space-y-6">
                <div className="space-y-4">
                    {invites.map((invite, index) => (
                        <div key={index} className="flex gap-3">
                            <div className="flex-1 space-y-2">
                                <Label htmlFor={`email-${index}`} className="sr-only">
                                    Email address
                                </Label>
                                <Input
                                    id={`email-${index}`}
                                    type="email"
                                    placeholder="colleague@example.com"
                                    value={invite.email}
                                    onChange={(e) => onEmailChange(index, e.target.value)}
                                    disabled={isLoading}
                                    required={invites.length > 1 || index === 0}
                                    className="bg-background"
                                />
                            </div>

                            <div className="w-40 space-y-2">
                                <Label htmlFor={`role-${index}`} className="sr-only">
                                    Role
                                </Label>
                                <select
                                    id={`role-${index}`}
                                    value={invite.role}
                                    onChange={(e) => onRoleChange(index, e.target.value as WorkspaceRole)}
                                    disabled={isLoading}
                                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value={WorkspaceRole.ADMINISTRATOR}>Admin</option>
                                    <option value={WorkspaceRole.COLLABORATOR}>Collaborator</option>
                                </select>
                            </div>

                            {invites.length > 1 && (
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    onClick={() => onRemoveInvite(index)}
                                    disabled={isLoading}
                                    className="shrink-0"
                                >
                                    <XIcon className="size-4" />
                                </Button>
                            )}
                        </div>
                    ))}
                </div>

                <Button
                    type="button"
                    variant="outline"
                    onClick={onAddInvite}
                    disabled={isLoading}
                    className="w-full"
                >
                    <PlusIcon className="mr-2 size-4" />
                    Add another member
                </Button>

                <div className="flex justify-between gap-3 pt-4">
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={onSkip}
                        disabled={isLoading}
                    >
                        Skip for now
                    </Button>

                    <Button
                        type="submit"
                        disabled={isLoading || !invites.some(i => i.email.trim())}
                        size="lg"
                    >
                        {isLoading ? 'Sending invitations...' : 'Send Invitations'}
                    </Button>
                </div>
            </form>
        </Card>
    );
};
