<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory()->expense(),
            'limit_amount' => $this->faker->randomFloat(2, 5000, 50000),
            'month' => (int) now()->format('n'),
            'year' => (int) now()->format('Y'),
            'notified_80' => false,
            'notified_100' => false,
        ];
    }
}
