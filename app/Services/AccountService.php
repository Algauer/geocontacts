<?php

namespace App\Services;

use App\Events\AccountSoftDeleted;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->contacts()->delete();
            $user->delete();
        });

        event(new AccountSoftDeleted($user->name, $user->email));
    }

    public function restoreAccount(string $email, string $password): ?array
    {
        $user = User::withTrashed()
            ->where('email', $email)
            ->first();

        if (! $user || ! $user->trashed() || ! Hash::check($password, $user->password)) {
            return null;
        }

        $deletedAt = CarbonImmutable::instance($user->deleted_at);
        $expiredAt = $deletedAt->addDays(7);

        if (CarbonImmutable::now()->greaterThan($expiredAt)) {
            return ['status' => 'expired'];
        }

        DB::transaction(function () use ($user) {
            $user->restore();
            $user->contacts()->withTrashed()->restore();
        });

        return [
            'status' => 'restored',
            'user' => $user->fresh(),
            'token' => $user->createToken('auth-token')->plainTextToken,
        ];
    }

    public function purgeExpiredAccounts(): int
    {
        $cutoff = CarbonImmutable::now()->subDays(7);

        $users = User::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->with(['contacts' => fn ($query) => $query->withTrashed()])
            ->get();

        foreach ($users as $user) {
            DB::transaction(function () use ($user) {
                $user->contacts()->withTrashed()->forceDelete();
                $user->tokens()->delete();
                $user->forceDelete();
            });
        }

        return $users->count();
    }
}
