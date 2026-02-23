<?php

namespace App\Listeners;

use App\Events\AccountSoftDeleted;
use App\Mail\AccountDeletedMail;
use Illuminate\Support\Facades\Mail;

class SendAccountDeletedEmail
{
    public function handle(AccountSoftDeleted $event): void
    {
        Mail::to($event->email)->send(new AccountDeletedMail(
            $event->name,
            $event->email,
            config('app.frontend_url') . '/restore-account'
        ));
    }
}
