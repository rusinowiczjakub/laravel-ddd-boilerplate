import AuthLayoutTemplate from '@/layouts/auth/auth-split-layout';
import {ToastProvider} from "@/components/providers/toast-provider";

export default function AuthLayout({children, title, description, ...props}: {
    children: React.ReactNode;
    title: string;
    description: string
}) {
    return (
        <ToastProvider>
            <AuthLayoutTemplate title={title} description={description} {...props}>
                {children}
            </AuthLayoutTemplate>
        </ToastProvider>
    );
}
