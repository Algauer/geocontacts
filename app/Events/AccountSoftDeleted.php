<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountSoftDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $name,
        public string $email
    ) {}
}
