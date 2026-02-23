<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth pública (sem middleware)
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'store']);
    Route::post('/login', [LoginController::class, 'store']);
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordController::class, 'reset']);
});

// Auth protegida (exige Bearer token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [LoginController::class, 'destroy']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Contatos
    Route::apiResource('contacts', ContactController::class);
    Route::get('/address/search', [AddressController::class, 'search']);
    Route::get('/address/{cep}', [AddressController::class, 'lookupCep'])->where('cep', '[0-9\-]+');
});
