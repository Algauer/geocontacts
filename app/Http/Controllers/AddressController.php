<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\SearchAddressRequest;
use App\Services\ViaCepService;
use RuntimeException;

class AddressController extends Controller
{
    public function __construct(
        private ViaCepService $viaCepService
    ) {}

    public function lookupCep(string $cep)
    {
        try {
            $address = $this->viaCepService->lookupCep($cep);

            if (! $address) {
                return response()->json(['message' => 'CEP nao encontrado.'], 404);
            }

            return response()->json(['data' => $address]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 502);
        }
    }

    public function search(SearchAddressRequest $request)
    {
        try {
            $addresses = $this->viaCepService->search(
                $request->string('uf')->toString(),
                $request->string('city')->toString(),
                $request->string('street')->toString()
            );

            return response()->json(['data' => $addresses]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 502);
        }
    }
}
