<?php

namespace App\Console\Commands;

use App\Services\AccountService;
use Illuminate\Console\Command;

class PurgeDeletedAccountsCommand extends Command
{
    protected $signature = 'accounts:purge';

    protected $description = 'Hard delete accounts and contacts soft deleted for more than 7 days.';

    public function handle(AccountService $accountService): int
    {
        $purged = $accountService->purgeExpiredAccounts();

        $this->info("Accounts purged: {$purged}");

        return self::SUCCESS;
    }
}
