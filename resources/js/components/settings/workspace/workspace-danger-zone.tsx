import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface WorkspaceDangerZoneProps {
    workspaceName: string;
    isOwner?: boolean;
    onLeave: () => void;
    onDelete: () => void;
}

export const WorkspaceDangerZone = ({ workspaceName, isOwner, onLeave, onDelete }: WorkspaceDangerZoneProps) => {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Danger zone</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className={`flex items-start justify-between gap-4 ${isOwner ? 'border-b pb-4' : ''}`}>
                    <div className="flex-1">
                        <h3 className="font-medium">
                            {isOwner ? 'Transfer ownership & leave' : 'Leave workspace'}
                        </h3>
                        <p className="text-muted-foreground text-sm">
                            {isOwner
                                ? `Before leaving, you must transfer ownership to another member of ${workspaceName}`
                                : `This action will immediately remove you from ${workspaceName}`}
                        </p>
                    </div>
                    <Button variant="outline" className="text-destructive hover:text-destructive" onClick={onLeave}>
                        {isOwner ? 'Transfer & leave' : 'Leave'}
                    </Button>
                </div>

                {isOwner && (
                    <div className="flex items-start justify-between gap-4">
                        <div className="flex-1">
                            <h3 className="font-medium">Delete workspace</h3>
                            <p className="text-muted-foreground text-sm">
                                This action will guide you through closing your workspace and scheduling your data for
                                permanent deletion.
                            </p>
                        </div>
                        <Button variant="outline" className="text-destructive hover:text-destructive" onClick={onDelete}>
                            Delete
                        </Button>
                    </div>
                )}
            </CardContent>
        </Card>
    );
};
