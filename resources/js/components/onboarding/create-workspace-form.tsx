import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert } from '@/components/ui/alert';
import { Building2Icon, AlertCircle } from 'lucide-react';
import type { FormEvent } from 'react';

interface CreateWorkspaceFormProps {
    name: string;
    onNameChange: (name: string) => void;
    onSubmit: (e: FormEvent) => void;
    isLoading?: boolean;
    error?: string | null;
}

export const CreateWorkspaceForm = ({
    name,
    onNameChange,
    onSubmit,
    isLoading = false,
    error = null,
}: CreateWorkspaceFormProps) => {
    return (
        <Card className="mx-auto max-w-2xl border-sidebar-border bg-sidebar p-8">
            <div className="mb-6 flex items-center gap-3">
                <div className="rounded-full bg-primary/10 p-3">
                    <Building2Icon className="size-6 text-primary" />
                </div>
                <div>
                    <h2 className="text-2xl font-bold">Create your workspace</h2>
                    <p className="text-sm text-muted-foreground">
                        A workspace is where your team collaborates on notifications
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
                <div className="space-y-2">
                    <Label htmlFor="workspace-name">Workspace name</Label>
                    <Input
                        id="workspace-name"
                        type="text"
                        placeholder="e.g., Acme Inc"
                        value={name}
                        onChange={(e) => onNameChange(e.target.value)}
                        disabled={isLoading}
                        required
                        autoFocus
                        className="bg-background"
                    />
                    <p className="text-xs text-muted-foreground">
                        You can always change this later in settings
                    </p>
                </div>

                <div className="flex justify-end gap-3">
                    <Button type="submit" disabled={isLoading || !name.trim()} size="lg">
                        {isLoading ? 'Creating...' : 'Create Workspace'}
                    </Button>
                </div>
            </form>
        </Card>
    );
};
