import { UsageCard } from '@/components/settings/usage';
import SettingsLayout from '@/layouts/settings-layout';
import { Head } from '@inertiajs/react';

interface UsageMetric {
    used: number;
    limit: number;
    percentage: number;
}

interface UsageSettingsProps {
    usage: {
        events: UsageMetric;
        notifications: UsageMetric;
        workflows: UsageMetric;
        members: UsageMetric;
    };
}

const defaultUsage: UsageSettingsProps['usage'] = {
    events: { used: 0, limit: 1000, percentage: 0 },
    notifications: { used: 0, limit: 500, percentage: 0 },
    workflows: { used: 0, limit: 5, percentage: 0 },
    members: { used: 1, limit: 1, percentage: 100 },
};

export default function UsageSettings({ usage = defaultUsage }: UsageSettingsProps) {
    return (
        <SettingsLayout>
            <Head title="Usage" />

            <div className="flex flex-col gap-6">
                <div>
                    <h1 className="text-3xl font-bold">Usage</h1>
                    <p className="text-muted-foreground">Monitor your workspace usage and limits.</p>
                </div>

                <UsageCard
                    title="Events"
                    description="Events ingested this month."
                    used={usage.events.used}
                    limit={usage.events.limit}
                    percentage={usage.events.percentage}
                    unit="events"
                />

                <UsageCard
                    title="Workflows"
                    description="Active workflows in your workspace."
                    used={usage.workflows.used}
                    limit={usage.workflows.limit}
                    percentage={usage.workflows.percentage}
                    unit="workflows"
                />

                <UsageCard
                    title="Team Members"
                    description="Active team members."
                    used={usage.members.used}
                    limit={usage.members.limit}
                    percentage={usage.members.percentage}
                    unit="members"
                />
            </div>
        </SettingsLayout>
    );
}
