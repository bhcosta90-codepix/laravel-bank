<?php

namespace Database\Factories;

use CodePix\System\Domain\Enum\EnumPixType;
use CodePix\System\Domain\Enum\EnumTransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank' => str()->uuid(),
            'status' => EnumTransactionStatus::OPEN,
            'reference' => str()->uuid(),
            'value' => $this->faker->numberBetween(100, 10000) / 100,
            'kind' => EnumPixType::ID,
            'key' => str()->uuid(),
            'description' => $this->faker->sentence(5),
        ];
    }
}
