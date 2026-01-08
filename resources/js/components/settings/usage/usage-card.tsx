import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';

interface UsageCardProps {
    title: string;
    description: string;
    used: number;
    limit: number;
    percentage: number;
    unit: string;
}

const formatNumber = (num: number): string => {
    if (num === -1) return 'Unlimited';
    return num.toLocaleString();
};

export const UsageCard = ({ title, description, used, limit, percentage, unit }: UsageCardProps) => {
    const isUnlimited = limit === -1;

    return (
        <Card>
            <CardHeader>
                <CardTitle>{title}</CardTitle>
                <CardDescription>{description}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="space-y-2">
                    <div className="flex justify-between text-sm">
                        <span>
                            {formatNumber(used)} / {formatNumber(limit)} {unit}
                        </span>
                        <span className="text-muted-foreground">
                            {isUnlimited ? '-' : `${percentage}%`}
                        </span>
                    </div>
                    <Progress value={isUnlimited ? 0 : percentage} />
                </div>
            </CardContent>
        </Card>
    );
};
