<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement([Transaction::TYPE_INCOME, Transaction::TYPE_EXPENSE]);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory()->state(['type' => $type]),
            'amount' => $this->faker->randomFloat(2, 100, 50000),
            'type' => $type,
            'transaction_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'note' => $this->faker->optional(0.4)->sentence(4),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => Transaction::TYPE_INCOME]);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => Transaction::TYPE_EXPENSE]);
    }
}
