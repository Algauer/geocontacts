<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', (string) $value);

        if (strlen($cpf) !== 11) {
            $fail('O CPF deve ter 11 digitos.');
            return;
        }

        // Rejeita sequencias com todos os digitos iguais (111.111.111-11, etc)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            $fail('O CPF informado e invalido.');
            return;
        }

        // Calcula primeiro digito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $firstDigit = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $cpf[9] !== $firstDigit) {
            $fail('O CPF informado e invalido.');
            return;
        }

        // Calcula segundo digito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $secondDigit = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $cpf[10] !== $secondDigit) {
            $fail('O CPF informado e invalido.');
        }
    }
}
