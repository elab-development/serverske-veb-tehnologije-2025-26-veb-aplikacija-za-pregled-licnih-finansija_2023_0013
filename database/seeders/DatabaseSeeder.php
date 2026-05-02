<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->seedTestUser('Marko Markovic', 'marko@test.com');
        $this->seedTestUser('Ana Anic', 'ana@test.com');
    }

    private function seedTestUser(string $name, string $email): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => 'password123',
        ]);

        $categories = $user->categories()->get()->keyBy('type');
        $expense = $user->categories()->where('type', Category::TYPE_EXPENSE)->get();
        $income = $user->categories()->where('type', Category::TYPE_INCOME)->get();

        for ($i = 0; $i < 50; $i++) {
            $isIncome = fake()->boolean(30);
            $category = $isIncome ? $income->random() : $expense->random();
            Transaction::factory()
                ->for($user)
                ->for($category)
                ->state(['type' => $isIncome ? Transaction::TYPE_INCOME : Transaction::TYPE_EXPENSE])
                ->create();
        }

        return $user;
    }
}
