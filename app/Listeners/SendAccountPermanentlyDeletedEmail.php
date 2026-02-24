<?php

namespace App\Listeners;

use App\Events\AccountDeleted;
use App\Mail\AccountPermanentlyDeletedMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendAccountPermanentlyDeletedEmail
{
    public function handle(AccountDeleted $event): void
    {
        $lockKey = 'mail:account-permanently-deleted:' . md5($event->email);
        if (! Cache::add($lockKey, true, now()->addMinutes(5))) {
            return;
        }

        Mail::to($event->email)->send(new AccountPermanentlyDeletedMail(
            $event->name,
            $event->email,
        ));
    }
}
