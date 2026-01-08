import { cn } from '@/lib/utils';
import { CheckCircle2, Clock, AlertTriangle, XCircle } from 'lucide-react';

interface SubscriptionStatusBadgeProps {
    status?: 'active' | 'trialing' | 'past_due' | 'canceled' | null;
    plan: string;
    endsAt?: string | null;
    className?: string;
}

export function SubscriptionStatusBadge({ status, plan, endsAt, className }: SubscriptionStatusBadgeProps) {
    // Free plan - no subscription needed
    if (plan === 'free') {
        return (
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-muted text-muted-foreground', className)}>
                Free Plan
            </span>
        );
    }

    // Paid plan with no subscription - inactive
    if (!status) {
        return (
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20', className)}>
                <XCircle className="h-3 w-3" />
                Inactive
            </span>
        );
    }

    // Active subscription - but check if it's being canceled
    if (status === 'active') {
        // If endsAt is set, subscription is active but will cancel at period end
        if (endsAt) {
            const endsAtDate = new Date(endsAt);
            const formattedDate = endsAtDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            return (
                <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20', className)}>
                    <Clock className="h-3 w-3" />
                    Cancels {formattedDate}
                </span>
            );
        }

        return (
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20', className)}>
                <CheckCircle2 className="h-3 w-3" />
                Active
            </span>
        );
    }

    // Trial period
    if (status === 'trialing') {
        return (
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20', className)}>
                <Clock className="h-3 w-3" />
                Trial
            </span>
        );
    }

    // Past due - payment failed
    if (status === 'past_due') {
        return (
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20', className)}>
                <AlertTriangle className="h-3 w-3" />
                Past Due
            </span>
        );
    }

    // Canceled - will end soon
    if (status === 'canceled') {
        const endsAtDate = endsAt ? new Date(endsAt) : null;
        const formattedDate = endsAtDate ? endsAtDate.toLocaleDateString() : '';

        return (
            <span className={cn('inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-500/10 text-gray-600 dark:text-gray-400 border border-gray-500/20', className)}>
                <XCircle className="h-3 w-3" />
                Ends {formattedDate}
            </span>
        );
    }

    return null;
}
