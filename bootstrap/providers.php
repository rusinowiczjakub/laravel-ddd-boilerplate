<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
    Modules\Billing\Infrastructure\Framework\Laravel\Providers\BillingServiceProvider::class,
    Modules\IAM\Infrastructure\Framework\Providers\IAMServiceProvider::class,
    Modules\Shared\Infrastructure\Framework\Laravel\Providers\SharedServiceProvider::class,
    Modules\Workspaces\Infrastructure\Framework\Providers\WorkspacesServiceProvider::class,
];
