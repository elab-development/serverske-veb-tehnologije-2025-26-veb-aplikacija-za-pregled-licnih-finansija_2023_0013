<?php

namespace Database\Seeders;

use App\Models\Budget;
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
            'points' => 0,
        ]);

        $this->seedTestUser('Marko Markovic', 'marko@test.com', 150);
        $this->seedTestUser('Ana Anic', 'ana@test.com', 620);
    }

    private function seedTestUser(string $name, string $email, int $points = 0): User
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

        $this->seedBudgetsFor($user);

        // Transakcije seedovane iznad su preko TransactionObserver-a vec dodale poene,
        // pa fiksiramo demo vrednost na kraju da prikaz na odbrani bude predvidiv.
        $user->update(['points' => $points]);

        return $user;
    }

    private function seedBudgetsFor(User $user): void
    {
        $names = ['Hrana', 'Kirija', 'Racuni', 'Transport'];
        $limits = [25000, 35000, 12000, 8000];

        foreach ($names as $i => $name) {
            $category = $user->categories()->where('name', $name)->first();
            if (! $category) {
                continue;
            }
            Budget::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'limit_amount' => $limits[$i],
                'month' => (int) now()->format('n'),
                'year' => (int) now()->format('Y'),
            ]);
        }
    }
}
