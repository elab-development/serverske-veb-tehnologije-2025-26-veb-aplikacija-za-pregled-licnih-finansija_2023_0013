<?php

namespace Tests\Browser;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_dashboard_prikazuje_4_KPI_kartice(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('BILANS')
                ->assertSee('PRIHODI MESECA')
                ->assertSee('RASHODI MESECA')
                ->assertSee('USTEDELI');

            $cards = $browser->script("return document.querySelectorAll('.kpi-card').length")[0];
            $this->assertEquals(4, $cards);
        });
    }

    public function test_donut_chart_canvas_postoji(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPresent('#categoryDonutChart')
                ->assertPresent('#monthlyLineChart');
        });
    }

    public function test_lista_poslednjih_5_transakcija(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($cat)->expense()->count(8)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard');
            $rows = $browser->script("return document.querySelectorAll('.transaction-row').length")[0];
            $this->assertEquals(5, $rows);
        });
    }

    public function test_status_budzeta_sekcija(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create(['name' => 'TestKategorija']);
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPresent('.budget-status-section')
                ->assertSee('TestKategorija');
        });
    }

    public function test_kpi_pokazuje_pravi_bilans(): void
    {
        $user = User::factory()->create();
        $income = Category::factory()->for($user)->income()->create();
        $expense = Category::factory()->for($user)->expense()->create();

        Transaction::factory()->for($user)->for($income)->income()->create(['amount' => 50000]);
        Transaction::factory()->for($user)->for($expense)->expense()->create(['amount' => 12000]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('38.000');
        });
    }
}
