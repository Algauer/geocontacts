<?php

namespace App\Providers;

use App\Events\AccountDeleted;
use App\Events\AccountSoftDeleted;
use App\Events\UserRegistered;
use App\Listeners\SendAccountDeletedEmail;
use App\Listeners\SendAccountPermanentlyDeletedEmail;
use App\Listeners\SendWelcomeOnboardingEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeOnboardingEmail::class,
        ],
        AccountSoftDeleted::class => [
            SendAccountDeletedEmail::class,
        ],
        AccountDeleted::class => [
            SendAccountPermanentlyDeletedEmail::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
