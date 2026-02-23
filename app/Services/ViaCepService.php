<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ViaCepService
{
    public function lookupCep(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);

        $response = Http::timeout(10)->get(
            sprintf('https://viacep.com.br/ws/%s/json/', $cep)
        );

        if ($response->failed()) {
            throw new RuntimeException('ViaCEP indisponivel no momento.');
        }

        $data = $response->json();

        if (! is_array($data) || isset($data['erro'])) {
            return null;
        }

        return $data;
    }

    public function search(string $uf, string $city, string $street): array
    {
        $response = Http::timeout(10)->get(
            sprintf(
                'https://viacep.com.br/ws/%s/%s/%s/json/',
                urlencode($uf),
                urlencode($city),
                urlencode($street)
            )
        );

        if ($response->failed()) {
            throw new RuntimeException('ViaCEP indisponivel no momento.');
        }

        $data = $response->json();

        if (! is_array($data)) {
            return [];
        }

        if (isset($data['erro']) && $data['erro'] === true) {
            return [];
        }

        return array_is_list($data) ? $data : [$data];
    }
}
