<?php

namespace Database\Factories;

use App\Models\Account;
use CodePix\Bank\Domain\Enum\EnumPixType;
use CodePix\Bank\Domain\Enum\EnumTransactionStatus;
use CodePix\Bank\Domain\Enum\EnumTransactionType;
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
            'account_id' => Account::factory(),
            'status' => EnumTransactionStatus::OPEN,
            'reference' => str()->uuid(),
            'value' => $this->faker->numberBetween(100, 10000) / 100,
            'kind' => EnumPixType::ID,
            'key' => str()->uuid(),
            'description' => $this->faker->sentence(5),
            'type' => EnumTransactionType::DEBIT,
        ];
    }
}
