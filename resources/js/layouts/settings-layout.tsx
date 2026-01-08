import {SettingsSidebar} from '@/components/settings-sidebar';
import {SidebarInset, SidebarProvider, SidebarTrigger} from '@/components/ui/sidebar';
import {usePage} from '@inertiajs/react';
import {PropsWithChildren} from 'react';
import {ToastProvider} from "@/components/providers/toast-provider";
import {FeedbackButton} from "@/components/feedback/feedback-button";

type Workspace = {
    id: string;
    name: string;
    slug: string;
    plan: string;
};

type SettingsLayoutProps = PropsWithChildren;

export default function SettingsLayout({children}: SettingsLayoutProps) {
    const {props} = usePage<{
        workspaces?: Workspace[];
        currentWorkspace?: Workspace;
    }>();

    return (
        <ToastProvider>
            <SidebarProvider>
                <SettingsSidebar workspaces={props.workspaces} currentWorkspace={props.currentWorkspace}/>
                <SidebarInset>
                    {/* Mobile header with menu trigger */}
                    <header className="flex h-14 items-center gap-2 border-b px-4 md:hidden">
                        <SidebarTrigger />
                        <span className="font-semibold">Settings</span>
                    </header>
                    <div
                        className="h-full overflow-x-clip overflow-y-auto rounded-xl border border-neutral-200 p-2 md:bg-neutral-50 dark:border-white/5 dark:md:bg-white/2">
                        <div className="mx-auto flex max-w-screen-lg flex-col gap-4 p-0 md:gap-8 md:p-12">
                            {children}
                        </div>
                    </div>
                </SidebarInset>
            </SidebarProvider>
            <FeedbackButton />
        </ToastProvider>
    );
}
