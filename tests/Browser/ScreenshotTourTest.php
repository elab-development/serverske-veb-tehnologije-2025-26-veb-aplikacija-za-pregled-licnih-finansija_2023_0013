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
        \App\Models\Transaction::factory()->count(3)->for($user)->for($expense)->expense()->create();
        \App\Models\Transaction::factory()->count(2)->for($user)->for($income)->income()->create();

        $dan = (int) (env('SCREENSHOT_DAN') ?? 4);

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
            'podesavanja' => '/profile',
        ];

        $this->browse(function (Browser $browser) use ($viewports, $pages, $user, $dan) {
            foreach ($viewports as $vpName => [$w, $h]) {
                $browser->resize($w, $h);

                foreach ($pages as $name => $url) {
                    if (in_array($url, ['/dashboard', '/categories', '/transactions', '/profile'], true)) {
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
