<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'cpf' => $this->generateValidCpf(),
            'phone' => fake()->numerify('###########'),
            'cep' => fake()->numerify('########'),
            'street' => fake()->streetName(),
            'number' => (string) fake()->numberBetween(1, 9999),
            'district' => fake()->citySuffix() . ' ' . fake()->lastName(),
            'city' => fake()->city(),
            'state' => fake()->randomElement([
                'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA',
                'MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN',
                'RS','RO','RR','SC','SP','SE','TO',
            ]),
            'complement' => null,
            'latitude' => fake()->latitude(-33, 5),
            'longitude' => fake()->longitude(-74, -35),
        ];
    }

    private function generateValidCpf(): string
    {
        $digits = [];
        for ($i = 0; $i < 9; $i++) {
            $digits[] = random_int(0, 9);
        }

        // Garante que nao sao todos iguais
        if (count(array_unique($digits)) === 1) {
            $digits[8] = ($digits[8] + 1) % 10;
        }

        // Primeiro digito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $digits[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digits[] = $remainder < 2 ? 0 : 11 - $remainder;

        // Segundo digito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $digits[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digits[] = $remainder < 2 ? 0 : 11 - $remainder;

        return implode('', $digits);
    }
}
