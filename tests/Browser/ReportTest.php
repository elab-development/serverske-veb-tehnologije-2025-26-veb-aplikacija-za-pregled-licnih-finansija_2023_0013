<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReportTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_filter_period_radi(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/reports')
                ->assertPresent('.report-filter')
                ->assertInputPresent('from')
                ->assertInputPresent('to')
                ->assertSee('Primeni');
        });
    }

    public function test_tabela_rezimea_prikazuje_kategorije(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create(['name' => 'RTesthrana']);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 1500,
            'transaction_date' => '2026-04-15',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/reports?from=2026-04-01&to=2026-04-30')
                ->assertSee('Rezime po kategorijama')
                ->assertSee('RTesthrana')
                ->assertSee('1.500,00');
        });
    }

    public function test_grafici_prisutni_kad_ima_podataka(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 2000,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/reports')
                ->assertPresent('#categoryBarChart')
                ->assertPresent('#balanceLineChart');
        });
    }

    public function test_empty_state_kad_nema_transakcija_u_periodu(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/reports?from=2020-01-01&to=2020-01-31')
                ->assertSee('nema transakcija');
        });
    }
}
