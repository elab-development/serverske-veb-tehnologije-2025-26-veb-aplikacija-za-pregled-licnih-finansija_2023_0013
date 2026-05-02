<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement([Category::TYPE_INCOME, Category::TYPE_EXPENSE]),
            'color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement(['bi-tag', 'bi-cart', 'bi-house', 'bi-receipt', 'bi-wallet', 'bi-bag']),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => Category::TYPE_INCOME]);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => Category::TYPE_EXPENSE]);
    }
}
