import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import SettingsLayout from '@/layouts/settings-layout';
import { Head } from '@inertiajs/react';

type NotificationSettingsProps = {
    workspaces?: Array<{
        id: string;
        name: string;
        slug: string;
        plan: string;
    }>;
    currentWorkspace?: {
        id: string;
        name: string;
        slug: string;
        plan: string;
    };
};

export default function NotificationSettings({ workspaces, currentWorkspace }: NotificationSettingsProps) {
    return (
        <SettingsLayout>
            <Head title="Notification Settings" />

            <div className="flex flex-col gap-6">
                <div>
                    <h1 className="text-3xl font-bold">Notifications</h1>
                    <p className="text-muted-foreground">Manage how you receive notifications.</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Email Notifications</CardTitle>
                        <CardDescription>Choose what emails you want to receive.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <div className="flex items-center justify-between">
                            <div className="space-y-0.5">
                                <Label>Workspace invitations</Label>
                                <p className="text-muted-foreground text-sm">
                                    Receive emails when you're invited to a workspace
                                </p>
                            </div>
                            <Switch defaultChecked />
                        </div>

                        <div className="flex items-center justify-between">
                            <div className="space-y-0.5">
                                <Label>Weekly summary</Label>
                                <p className="text-muted-foreground text-sm">Get a weekly summary of your workspace activity</p>
                            </div>
                            <Switch defaultChecked />
                        </div>

                        <div className="flex items-center justify-between">
                            <div className="space-y-0.5">
                                <Label>Marketing emails</Label>
                                <p className="text-muted-foreground text-sm">Receive updates about new features and products</p>
                            </div>
                            <Switch />
                        </div>

                        <div className="flex justify-end">
                            <Button>Save preferences</Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    );
}
