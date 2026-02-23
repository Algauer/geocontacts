<?php

namespace Tests\Feature\Auth;

use App\Models\Contact;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurgeDeletedAccountsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_command_removes_accounts_deleted_more_than_seven_days_ago(): void
    {
        $oldDeletedAt = CarbonImmutable::parse('2026-01-01 10:00:00');

        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $user->delete();
        $contact->delete();

        DB::table('users')->where('id', $user->id)->update(['deleted_at' => $oldDeletedAt]);
        DB::table('contacts')->where('id', $contact->id)->update(['deleted_at' => $oldDeletedAt]);

        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-01-09 10:00:01'));

        $this->artisan('accounts:purge')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);

        CarbonImmutable::setTestNow();
    }

    public function test_purge_command_keeps_recently_deleted_accounts(): void
    {
        $recentDeletedAt = CarbonImmutable::parse('2026-01-08 10:00:00');

        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $user->delete();
        $contact->delete();

        DB::table('users')->where('id', $user->id)->update(['deleted_at' => $recentDeletedAt]);
        DB::table('contacts')->where('id', $contact->id)->update(['deleted_at' => $recentDeletedAt]);

        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-01-10 09:00:00'));

        $this->artisan('accounts:purge')
            ->assertExitCode(0);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);

        CarbonImmutable::setTestNow();
    }
}
