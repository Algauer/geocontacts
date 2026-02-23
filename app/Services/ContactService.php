<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ContactService
{
    public function __construct(
        private GeocodingService $geocodingService
    ) {}

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
        $payload = $this->resolveCoordinates($data);

        return Contact::create([
            ...$payload,
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
        $payload = $this->resolveCoordinates($data);

        $contact->update($payload);

        return $contact->fresh();
    }

    public function delete(Contact $contact): void
    {
        $contact->delete();
    }

    private function resolveCoordinates(array $data): array
    {
        if (isset($data['latitude'], $data['longitude'])) {
            return $data;
        }

        $coordinates = $this->geocodingService->geocode(
            $data['street'],
            $data['number'],
            $data['district'],
            $data['city'],
            $data['state'],
            $data['cep']
        );

        if (! $coordinates) {
            throw ValidationException::withMessages([
                'address' => 'Nao foi possivel obter coordenadas para o endereco informado.',
            ]);
        }

        return [
            ...$data,
            ...$coordinates,
        ];
    }
}
