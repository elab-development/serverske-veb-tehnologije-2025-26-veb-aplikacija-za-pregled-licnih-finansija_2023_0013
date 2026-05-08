<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_vidi_listu_korisnika(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $userA = User::factory()->create(['name' => 'TestKorisnikA']);
        User::factory()->create(['name' => 'TestKorisnikB']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin')
                ->assertSee('Admin panel')
                ->assertSee('TestKorisnikA')
                ->assertSee('TestKorisnikB')
                ->assertPresent('.admin-stats')
                ->assertPresent('.admin-users');
        });
    }

    public function test_obican_korisnik_dobija_403_na_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_deaktivira_korisnika(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['name' => 'KorisnikZaDeaktivaciju', 'is_active' => true]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin')
                ->assertSee('KorisnikZaDeaktivaciju');

            $browser->script("
                const rows = [...document.querySelectorAll('.user-row')];
                const target = rows.find(r => r.textContent.includes('KorisnikZaDeaktivaciju'));
                target.querySelector('form').submit();
            ");
            $browser->waitForLocation('/admin', 5);
        });

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    public function test_deaktivirani_korisnik_ne_moze_da_se_uloguje(): void
    {
        User::factory()->create([
            'email' => 'deaktivirani@test.com',
            'password' => bcrypt('lozinka123'),
            'is_active' => false,
        ]);

        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/login')
                ->type('email', 'deaktivirani@test.com')
                ->type('password', 'lozinka123')
                ->click('button[type=submit]')
                ->pause(1500)
                ->assertSee('deaktiviran');
        });
    }

    public function test_statistika_pokazuje_brojeve(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->count(2)->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin')
                ->assertSee('KORISNIKA')
                ->assertSee('TRANSAKCIJA');
        });
    }
}
