<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CategoryTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_korisnik_vidi_default_kategorije_razdvojene_po_tipu(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/categories')
                ->assertSee('Kategorije')
                ->assertSee('Prihodi')
                ->assertSee('Rashodi')
                ->assertSee('Plata')
                ->assertSee('Hrana');
        });
    }

    public function test_korisnik_moze_da_doda_novu_kategoriju(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/categories/create')
                ->type('name', 'Sport')
                ->select('type', 'expense')
                ->script("document.querySelector('input[name=color]').value = '#3B82F6'");

            $browser->press('Sacuvaj')
                ->waitForLocation('/categories', 5)
                ->assertSee('Sport');
        });

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Sport',
            'type' => 'expense',
        ]);
    }

    public function test_korisnik_moze_da_izmeni_kategoriju(): void
    {
        $user = User::factory()->create();
        $category = $user->categories()->where('name', 'Hrana')->first();

        $this->browse(function (Browser $browser) use ($user, $category) {
            $browser->loginAs($user)
                ->visit('/categories/'.$category->id.'/edit')
                ->assertInputValue('name', 'Hrana')
                ->type('name', 'Namirnice')
                ->press('Sacuvaj')
                ->waitForLocation('/categories', 5)
                ->assertSee('Namirnice');
        });

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Namirnice',
        ]);
    }

    public function test_brisanje_kategorije_bez_transakcija_uspesno(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create(['name' => 'ZaBrisanje']);

        $this->browse(function (Browser $browser) use ($user, $category) {
            $browser->loginAs($user)
                ->visit('/categories')
                ->assertSee('ZaBrisanje')
                ->script("
                    const form = document.querySelector('form[action*=\"/categories/{$category->id}\"]');
                    form.removeAttribute('onsubmit');
                    form.submit();
                ");
            $browser->waitForLocation('/categories', 5)
                ->assertDontSee('ZaBrisanje');
        });

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_brisanje_kategorije_sa_transakcijama_blokirano(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create(['name' => 'SaTransakcijama']);
        Transaction::factory()->for($user)->for($category)->expense()->create();

        $this->browse(function (Browser $browser) use ($user, $category) {
            $browser->loginAs($user)
                ->visit('/categories')
                ->assertSee('SaTransakcijama')
                ->script("
                    const form = document.querySelector('form[action*=\"/categories/{$category->id}\"]');
                    form.removeAttribute('onsubmit');
                    form.submit();
                ");
            $browser->waitForLocation('/categories', 5)
                ->assertSee('SaTransakcijama')
                ->assertSee('transakcija');
        });

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
