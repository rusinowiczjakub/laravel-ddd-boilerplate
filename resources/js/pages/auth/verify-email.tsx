import { FormEventHandler, useEffect, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { OtpInput } from '@/components/ui/otp-input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';

export default function VerifyEmail({ email, status }: { email: string; status?: string }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: email,
        code: '',
    });

    const [resendStatus, setResendStatus] = useState<string | null>(null);
    const [canResend, setCanResend] = useState(true);
    const [countdown, setCountdown] = useState(0);

    useEffect(() => {
        if (countdown > 0) {
            const timer = setTimeout(() => setCountdown(countdown - 1), 1000);
            return () => clearTimeout(timer);
        } else {
            setCanResend(true);
        }
    }, [countdown]);

    // Auto-submit when 8 characters are entered
    useEffect(() => {
        if (data.code.length === 8) {
            post(route('verify-email.verify'), {
                onSuccess: () => {
                    reset();
                },
                onError: () => {
                    setData('code', '');
                },
            });
        }
    }, [data.code]);

    const handleSubmit: FormEventHandler = (e) => {
        e.preventDefault();
        if (data.code.length === 8) {
            post(route('verify-email.verify'), {
                onSuccess: () => {
                    reset();
                },
                onError: () => {
                    setData('code', '');
                },
            });
        }
    };

    const handleResend = () => {
        if (!canResend) return;

        setCanResend(false);
        setCountdown(60);
        setResendStatus(null);

        post(route('verify-email.resend'), {
            preserveScroll: true,
            onSuccess: () => {
                setResendStatus('success');
                setTimeout(() => setResendStatus(null), 3000);
            },
            onError: () => {
                setResendStatus('error');
                setCanResend(true);
                setCountdown(0);
            },
        });
    };

    return (
        <>
            <Head title="Verify Email" />

            <div className="flex min-h-screen items-center justify-center bg-neutral-50 p-4 dark:bg-neutral-900">
                <Card className="w-full max-w-md">
                    <CardHeader className="text-center">
                        <CardTitle className="text-2xl">Check your email</CardTitle>
                        <CardDescription>
                            We sent a verification code to{' '}
                            <span className="font-medium text-foreground">{email}</span>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            {status === 'verification-link-sent' && (
                                <Alert>
                                    <AlertDescription>
                                        A new verification code has been sent to your email address.
                                    </AlertDescription>
                                </Alert>
                            )}

                            {errors.code && (
                                <Alert variant="destructive">
                                    <AlertDescription>{errors.code}</AlertDescription>
                                </Alert>
                            )}

                            {resendStatus === 'error' && (
                                <Alert variant="destructive">
                                    <AlertDescription>
                                        Failed to resend verification code. Please try again.
                                    </AlertDescription>
                                </Alert>
                            )}

                            <div className="space-y-2">
                                <Label>Verification code</Label>
                                <OtpInput
                                    value={data.code}
                                    onChange={(value) => setData('code', value)}
                                    disabled={processing}
                                    autoFocus
                                />
                                <p className="text-muted-foreground text-center text-sm">
                                    Enter the 8-character code from your email
                                </p>
                            </div>

                            <div className="flex items-center justify-between text-sm">
                                <span className="text-muted-foreground">
                                    Didn't receive the code?
                                </span>
                                <Button
                                    type="button"
                                    variant="link"
                                    onClick={handleResend}
                                    disabled={!canResend || processing}
                                    className="h-auto p-0"
                                >
                                    {countdown > 0 ? `Resend in ${countdown}s` : 'Resend code'}
                                </Button>
                            </div>

                            <Button
                                type="submit"
                                className="w-full"
                                disabled={processing || data.code.length !== 8}
                            >
                                {processing ? 'Verifying...' : 'Verify email'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
