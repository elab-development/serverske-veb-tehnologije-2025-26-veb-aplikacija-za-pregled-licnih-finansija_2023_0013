<?php

namespace Tests\Browser;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetThresholdNotification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_notifikacija_se_okida_na_85_posto(): void
    {
        $user = User::factory()->create(['email_notifications' => false]);
        $cat = Category::factory()->for($user)->expense()->create(['name' => 'TestHrana']);
        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $this->browse(function (Browser $browser) use ($user, $cat) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->click('.new-transaction-btn')
                ->waitFor('#newTransactionModal', 3)
                ->script("
                    const sel = document.querySelector('#newTransactionModal select[name=category_id]');
                    sel.value = '{$cat->id}';
                    sel.dispatchEvent(new Event('change'));
                    const a = document.querySelector('#newTransactionModal input[name=amount]');
                    a.value = '8500';
                    a.dispatchEvent(new Event('input'));
                ");
            $browser->click('#newTransactionModal button[type=submit]')
                ->waitForLocation('/transactions', 5);
        });

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => BudgetThresholdNotification::class,
        ]);
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'notified_80' => true,
            'notified_100' => false,
        ]);
    }

    public function test_druga_notifikacija_na_105_posto(): void
    {
        $user = User::factory()->create(['email_notifications' => false]);
        $cat = Category::factory()->for($user)->expense()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
            'notified_80' => true,
        ]);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 8500,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user, $cat) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->click('.new-transaction-btn')
                ->waitFor('#newTransactionModal', 3)
                ->script("
                    const sel = document.querySelector('#newTransactionModal select[name=category_id]');
                    sel.value = '{$cat->id}';
                    sel.dispatchEvent(new Event('change'));
                    const a = document.querySelector('#newTransactionModal input[name=amount]');
                    a.value = '2000';
                    a.dispatchEvent(new Event('input'));
                ");
            $browser->click('#newTransactionModal button[type=submit]')
                ->waitForLocation('/transactions', 5);
        });

        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'notified_100' => true,
        ]);
    }

    public function test_ne_okida_se_dva_puta_iznad_100(): void
    {
        $user = User::factory()->create(['email_notifications' => false]);
        $cat = Category::factory()->for($user)->expense()->create();
        Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
            'notified_80' => true,
            'notified_100' => true,
        ]);
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 10500,
            'transaction_date' => now()->format('Y-m-d'),
        ]);

        $this->browse(function (Browser $browser) use ($user, $cat) {
            $browser->loginAs($user)
                ->visit('/transactions')
                ->click('.new-transaction-btn')
                ->waitFor('#newTransactionModal', 3)
                ->script("
                    const sel = document.querySelector('#newTransactionModal select[name=category_id]');
                    sel.value = '{$cat->id}';
                    sel.dispatchEvent(new Event('change'));
                    const a = document.querySelector('#newTransactionModal input[name=amount]');
                    a.value = '500';
                    a.dispatchEvent(new Event('input'));
                ");
            $browser->click('#newTransactionModal button[type=submit]')
                ->waitForLocation('/transactions', 5);
        });

        $this->assertEquals(0, \DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->count(), 'Nema novih notifikacija nakon vec setovan flag');
    }

    public function test_email_kanal_iskljucen_kad_korisnik_iskljuci_toggle(): void
    {
        $user = User::factory()->create(['email_notifications' => false]);
        $cat = Category::factory()->for($user)->expense()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $notification = new BudgetThresholdNotification($budget, 80, 8500);
        $channels = $notification->via($user);

        $this->assertContains('database', $channels);
        $this->assertNotContains('mail', $channels);
    }

    public function test_zvonce_prikazuje_broj_neprocitanih(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'limit_amount' => 10000,
            'month' => now()->month,
            'year' => now()->year,
        ]);
        $user->notify(new BudgetThresholdNotification($budget, 80, 8500));

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/categories')
                ->assertPresent('.notifications-bell')
                ->assertSeeIn('.notifications-count', '1');
        });
    }
}
