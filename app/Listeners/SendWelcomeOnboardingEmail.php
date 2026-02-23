<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\WelcomeOnboardingMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendWelcomeOnboardingEmail
{
    public function handle(UserRegistered $event): void
    {
        $lockKey = 'mail:welcome:' . $event->user->id;
        if (! Cache::add($lockKey, true, now()->addMinutes(5))) {
            return;
        }

        Mail::to($event->user->email)->send(new WelcomeOnboardingMail($event->user));
    }
}
