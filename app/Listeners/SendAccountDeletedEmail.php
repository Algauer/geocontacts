<?php

namespace App\Listeners;

use App\Events\AccountSoftDeleted;
use App\Mail\AccountDeletedMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendAccountDeletedEmail
{
    public function handle(AccountSoftDeleted $event): void
    {
        $lockKey = 'mail:account-deleted:' . md5($event->email);
        if (! Cache::add($lockKey, true, now()->addMinutes(5))) {
            return;
        }

        Mail::to($event->email)->send(new AccountDeletedMail(
            $event->name,
            $event->email,
            config('app.frontend_url') . '/restore-account'
        ));
    }
}
