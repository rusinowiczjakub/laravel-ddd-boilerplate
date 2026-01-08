import { Head, router } from '@inertiajs/react';
import { useEffect } from 'react';
import { Loader2 } from 'lucide-react';

interface CheckoutPageProps {
    plan: string;
    workspaceCreated: boolean;
}

export default function Checkout({ plan, workspaceCreated }: CheckoutPageProps) {
    useEffect(() => {
        if (workspaceCreated) {
            // Auto-submit to billing checkout
            router.post('/billing/checkout', {
                plan,
                billing_period: 'monthly',
            });
        }
    }, [workspaceCreated, plan]);

    return (
        <>
            <Head title="Processing Checkout - NotifyHub" />

            <div className="min-h-screen flex items-center justify-center bg-background">
                <div className="text-center space-y-4">
                    <Loader2 className="h-12 w-12 animate-spin mx-auto text-primary" />
                    <h1 className="text-2xl font-semibold text-foreground">
                        Redirecting to checkout...
                    </h1>
                    <p className="text-muted-foreground">
                        Please wait while we prepare your {plan} plan checkout.
                    </p>
                </div>
            </div>
        </>
    );
}
