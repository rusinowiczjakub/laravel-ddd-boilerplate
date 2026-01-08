import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Method, VisitOptions } from '@inertiajs/core';

export function useRouterAction() {
    const [processing, setProcessing] = useState(false);

    const submit = (method: Method, url: string, options?: VisitOptions) => {
        router.visit(url, {
            ...options,
            method,
            onStart: () => {
                setProcessing(true);
                options?.onStart?.();
            },
            onFinish: () => {
                setProcessing(false);
                options?.onFinish?.();
            },
        });
    };

    const post = (url: string, options?: VisitOptions) => submit('post', url, options);
    const put = (url: string, options?: VisitOptions) => submit('put', url, options);
    const patch = (url: string, options?: VisitOptions) => submit('patch', url, options);
    const del = (url: string, options?: VisitOptions) => submit('delete', url, options);

    return {
        processing,
        post,
        put,
        patch,
        delete: del,
    };
}
