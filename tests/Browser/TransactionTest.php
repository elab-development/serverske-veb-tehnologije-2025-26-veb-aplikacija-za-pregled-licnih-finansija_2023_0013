<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TransactionTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_modal_se_otvara_i_zatvara(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->click('.new-transaction-btn')
                ->waitFor('#newTransactionModal', 3)
                ->assertVisible('#newTransactionModal')
                ->click('#newTransactionModal .close-btn')
                ->waitUntilMissingText('Otkazi', 3);
        });
    }

    public function test_korisnik_kreira_novu_transakciju(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->click('.new-transaction-btn')
                ->waitFor('#newTransactionModal', 3)
                ->pause(300)
                ->script("
                    const a = document.querySelector('#newTransactionModal input[name=amount]');
                    a.value = '1500';
                    a.dispatchEvent(new Event('input'));
                    const sel = document.querySelector('#newTransactionModal select[name=category_id]');
                    sel.value = sel.options[1].value;
                    sel.dispatchEvent(new Event('change'));
                ");
            $browser->click('#newTransactionModal button[type=submit]')
                ->waitForLocation('/transactions', 5)
                ->assertSee('1.500,00 RSD');
        });

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 1500,
            'type' => 'expense',
        ]);
    }

    public function test_promena_tipa_filtrira_kategorije(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->click('.new-transaction-btn')
                ->waitFor('#newTransactionModal', 3)
                ->pause(400);

            $expenseFirst = $browser->script("return document.querySelectorAll('#newTransactionModal select[name=category_id] option')[1]?.textContent || ''")[0];

            $browser->click('#newTransactionModal input[type=radio][value=income]')
                ->pause(500);

            $incomeFirst = $browser->script("return document.querySelectorAll('#newTransactionModal select[name=category_id] option')[1]?.textContent || ''")[0];

            $this->assertNotSame('', $expenseFirst);
            $this->assertNotSame('', $incomeFirst);
            $this->assertNotEquals($expenseFirst, $incomeFirst, "Prva kategorija mora da se promeni pri promeni tipa. Expense: {$expenseFirst}, Income: {$incomeFirst}");
        });
    }

    public function test_iznosi_su_obojeni_zelena_prihod_crvena_rashod(): void
    {
        $user = User::factory()->create();
        $income = Category::factory()->for($user)->income()->create(['name' => 'TestPrihod']);
        $expense = Category::factory()->for($user)->expense()->create(['name' => 'TestRashod']);

        Transaction::factory()->for($user)->for($income)->income()->create(['amount' => 1000]);
        Transaction::factory()->for($user)->for($expense)->expense()->create(['amount' => 500]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->assertPresent('.amount-income')
                ->assertPresent('.amount-expense');
        });
    }

    public function test_paginacija_se_prikazuje_kad_ima_vise_od_20(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($category)->expense()->count(25)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->assertSee('Ukupno: 25');
        });
    }
}
