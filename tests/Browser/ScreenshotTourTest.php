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

        $dan = (int) (env('SCREENSHOT_DAN') ?? 3);

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
            'kategorija-create' => '/categories/create',
            'podesavanja' => '/profile',
        ];

        $this->browse(function (Browser $browser) use ($viewports, $pages, $user, $dan) {
            foreach ($viewports as $vpName => [$w, $h]) {
                $browser->resize($w, $h);

                foreach ($pages as $name => $url) {
                    if (in_array($url, ['/dashboard', '/categories', '/categories/create', '/profile'], true)) {
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
