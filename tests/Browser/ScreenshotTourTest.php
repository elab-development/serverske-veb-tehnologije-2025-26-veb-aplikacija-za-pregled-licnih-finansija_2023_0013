<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScreenshotTourTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_screenshot_tour(): void
    {
        $user = User::factory()->create([
            'name' => 'Marko Markovic',
            'email' => 'marko@test.com',
        ]);

        $expense = $user->categories()->where('name', 'Hrana')->first();
        $income = $user->categories()->where('name', 'Plata')->first();
        $kirija = $user->categories()->where('name', 'Kirija')->first();
        \App\Models\Transaction::factory()->count(3)->for($user)->for($expense)->expense()->create([
            'amount' => 3000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);
        \App\Models\Transaction::factory()->count(2)->for($user)->for($income)->income()->create();
        \App\Models\Budget::create([
            'user_id' => $user->id,
            'category_id' => $expense->id,
            'limit_amount' => 12000,
            'month' => now()->month,
            'year' => now()->year,
        ]);
        \App\Models\Budget::create([
            'user_id' => $user->id,
            'category_id' => $kirija->id,
            'limit_amount' => 30000,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $dan = (int) (env('SCREENSHOT_DAN') ?? 6);

        $viewports = [
            'mobile' => [375, 667],
            'tablet' => [768, 1024],
            'desktop' => [1280, 800],
        ];

        $pages = [
            'pocetna' => '/',
            'login' => '/login',
            'register' => '/register',
            'dashboard' => '/dashboard',
            'kategorije' => '/categories',
            'transakcije' => '/transactions',
            'transakcije-filter' => '/transactions?type=income',
            'budzeti' => '/budgets',
            'podesavanja' => '/profile',
        ];

        $this->browse(function (Browser $browser) use ($viewports, $pages, $user, $dan) {
            foreach ($viewports as $vpName => [$w, $h]) {
                $browser->resize($w, $h);

                foreach ($pages as $name => $url) {
                    if (str_starts_with($url, '/dashboard') || str_starts_with($url, '/categories') || str_starts_with($url, '/transactions') || str_starts_with($url, '/budgets') || str_starts_with($url, '/profile')) {
                        $browser->loginAs($user);
                    } else {
                        $browser->logout();
                    }

                    $browser->visit($url)
                        ->pause(400)
                        ->screenshot("dan-{$dan}/{$name}-{$vpName}");
                }
            }
        });
    }
}
