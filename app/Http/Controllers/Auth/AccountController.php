<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DeleteAccountRequest;
use App\Http\Requests\Auth\RestoreAccountRequest;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        $this->accountService->deleteUser(
            $request->user(),
            $request->boolean('immediate'),
        );

        return response()->json(null, 204);
    }

    public function restore(RestoreAccountRequest $request): JsonResponse
    {
        $result = $this->accountService->restoreAccount(
            $request->string('email')->toString(),
            $request->string('password')->toString()
        );

        if (! $result) {
            return response()->json([
                'message' => 'Credenciais invalidas.',
            ], 401);
        }

        if ($result['status'] === 'expired') {
            return response()->json([
                'message' => 'Prazo de restauracao expirado.',
            ], 410);
        }

        $user = $result['user'];

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $result['token'],
        ]);
    }
}
