<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TransactionFilterTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_filter_po_tipu_skriva_drugi_tip(): void
    {
        $user = User::factory()->create();
        $income = Category::factory()->for($user)->income()->create();
        $expense = Category::factory()->for($user)->expense()->create();

        Transaction::factory()->for($user)->for($income)->income()->create(['amount' => 1000, 'note' => 'plata mart']);
        Transaction::factory()->for($user)->for($expense)->expense()->create(['amount' => 500, 'note' => 'racun struja']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->assertSee('plata mart')
                ->assertSee('racun struja')
                ->visit('/transactions?type=income')
                ->assertSee('plata mart')
                ->assertDontSee('racun struja');
        });
    }

    public function test_filter_po_kategoriji(): void
    {
        $user = User::factory()->create();
        $catA = Category::factory()->for($user)->expense()->create(['name' => 'Hrana']);
        $catB = Category::factory()->for($user)->expense()->create(['name' => 'Kirija']);

        Transaction::factory()->for($user)->for($catA)->expense()->create(['note' => 'pijaca']);
        Transaction::factory()->for($user)->for($catB)->expense()->create(['note' => 'mesec maj']);

        $this->browse(function (Browser $browser) use ($user, $catA) {
            $browser->loginAs($user)
                ->visit('/transactions?category_id='.$catA->id)
                ->assertSee('pijaca')
                ->assertDontSee('mesec maj');
        });
    }

    public function test_pretraga_po_napomeni(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();

        Transaction::factory()->for($user)->for($cat)->expense()->create(['note' => 'kupovina knjige']);
        Transaction::factory()->for($user)->for($cat)->expense()->create(['note' => 'kafa kod komsije']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions?q=knjig')
                ->assertSee('kupovina knjige')
                ->assertDontSee('kafa kod komsije');
        });
    }

    public function test_filter_po_datumskom_opsegu(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();

        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'transaction_date' => '2026-01-15',
            'note' => 'januar transakcija',
        ]);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'transaction_date' => '2026-04-15',
            'note' => 'april transakcija',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions?from=2026-04-01&to=2026-04-30')
                ->assertSee('april transakcija')
                ->assertDontSee('januar transakcija');
        });
    }

    public function test_reset_filtera_vraca_sve_transakcije(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($cat)->expense()->count(3)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions?type=income')
                ->assertSee('Resetuj')
                ->click('.reset-filters')
                ->waitForLocation('/transactions', 5)
                ->assertSee('Ukupno: 3');
        });
    }

    public function test_paginacija_zadrzava_filter(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create(['name' => 'Hrana']);
        Transaction::factory()->for($user)->for($cat)->expense()->count(25)->create();

        $this->browse(function (Browser $browser) use ($user, $cat) {
            $browser->loginAs($user)
                ->visit('/transactions?category_id='.$cat->id)
                ->assertSee('Ukupno: 25')
                ->assertSourceHas('category_id='.$cat->id);
        });
    }
}
