<?php

namespace Tests\Browser;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BudgetTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_progress_bar_zelena_ispod_80_posto(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create(['name' => 'Hrana']);
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 5000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/budgets')
                ->assertPresent('.progress-success');
        });
    }

    public function test_progress_bar_zuta_iznad_80_posto(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 8500,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/budgets')
                ->assertPresent('.progress-warning');
        });
    }

    public function test_progress_bar_crvena_iznad_100_posto(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 11000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/budgets')
                ->assertPresent('.progress-danger');
        });
    }

    public function test_korisnik_dodaje_novi_budzet(): void
    {
        $user = User::factory()->create();
        $cat = $user->categories()->where('name', 'Transport')->first();

        $this->browse(function (Browser $browser) use ($user, $cat) {
            $browser->loginAs($user)
                ->visit('/budgets')
                ->click('.new-budget-btn')
                ->waitFor('#budgetModal', 3)
                ->script("
                    const sel = document.querySelector('#budgetModal select[name=category_id]');
                    sel.value = '{$cat->id}';
                    sel.dispatchEvent(new Event('change'));
                    const a = document.querySelector('#budgetModal input[name=limit_amount]');
                    a.value = '8000';
                    a.dispatchEvent(new Event('input'));
                ");
            $browser->click('#budgetModal button[type=submit]')
                ->waitForLocation('/budgets', 5)
                ->assertSee('Transport')
                ->assertSee('8.000');
        });

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 8000,
        ]);
    }

    public function test_kopiraj_iz_prethodnog_meseca(): void
    {
        $user = User::factory()->create();
        $cat = $user->categories()->where('name', 'Hrana')->first();

        $prevMonth = now()->subMonth();
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 15000,
            'month' => $prevMonth->month,
            'year' => $prevMonth->year,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/budgets?month='.now()->month.'&year='.now()->year)
                ->assertSee('nema budzeta')
                ->click('.copy-previous-btn')
                ->waitForLocation('/budgets', 5)
                ->assertSee('Kopirano je 1');
        });

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'month' => now()->month,
            'year' => now()->year,
            'limit_amount' => 15000,
        ]);
    }
}
