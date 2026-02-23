<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class GeocodingService
{
    public function geocode(
        string $street,
        string $number,
        string $district,
        string $city,
        string $state,
        string $cep
    ): ?array {
        $address = trim(sprintf(
            '%s, %s, %s, %s - %s, %s, Brasil',
            $street,
            $number,
            $district,
            $city,
            $state,
            $cep
        ));

        $query = ['address' => $address];
        $key = config('services.google_maps.key');

        if (! empty($key)) {
            $query['key'] = $key;
        }

        try {
            $response = Http::timeout(10)
                ->get('https://maps.googleapis.com/maps/api/geocode/json', $query);
        } catch (Throwable) {
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        $payload = $response->json();

        if (($payload['status'] ?? null) !== 'OK') {
            return null;
        }

        $location = $payload['results'][0]['geometry']['location'] ?? null;

        if (! isset($location['lat'], $location['lng'])) {
            return null;
        }

        return [
            'latitude' => (float) $location['lat'],
            'longitude' => (float) $location['lng'],
        ];
    }
}
