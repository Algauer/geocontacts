<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\WelcomeOnboardingMail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeOnboardingEmail
{
    public function handle(UserRegistered $event): void
    {
        Mail::to($event->user->email)->send(new WelcomeOnboardingMail($event->user));
    }
}
