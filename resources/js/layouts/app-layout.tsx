import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import {type BreadcrumbItem} from '@/types';
import {type ReactNode} from 'react';
import {ToastProvider} from "@/components/providers/toast-provider";
import {FeedbackButton} from "@/components/feedback/feedback-button";

interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({children, breadcrumbs, ...props}: AppLayoutProps) => (
    <ToastProvider>
        <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
            {children}
        </AppLayoutTemplate>
        <FeedbackButton />
    </ToastProvider>
);
