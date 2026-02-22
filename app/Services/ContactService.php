<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactService
{
    public function list(string $userId, ?string $search = null): LengthAwarePaginator
    {
        $query = Contact::where('user_id', $userId)
            ->orderBy('name', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        return $query->paginate(15);
    }

    public function create(array $data, string $userId): Contact
    {
        return Contact::create([
            ...$data,
            'user_id' => $userId,
        ]);
    }

    public function findForUser(string $contactId, string $userId): ?Contact
    {
        return Contact::where('id', $contactId)
            ->where('user_id', $userId)
            ->first();
    }

    public function update(Contact $contact, array $data): Contact
    {
        $contact->update($data);

        return $contact->fresh();
    }

    public function delete(Contact $contact): void
    {
        $contact->delete();
    }
}
