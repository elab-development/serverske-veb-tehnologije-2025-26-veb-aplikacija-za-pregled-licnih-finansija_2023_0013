<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExportTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_pdf_dugme_postoji_na_izvestajima(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/reports')
                ->assertPresent('.export-pdf-btn')
                ->assertPresent('.export-excel-btn');
        });
    }

    public function test_pdf_endpoint_vraca_pdf_content_type(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 1500,
            'transaction_date' => '2026-04-15',
        ]);

        $response = $this->actingAs($user)->get('/reports/export/pdf?from=2026-04-01&to=2026-04-30');

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_excel_endpoint_vraca_xlsx(): void
    {
        $user = User::factory()->create();
        $cat = Category::factory()->for($user)->expense()->create();
        Transaction::factory()->for($user)->for($cat)->expense()->create([
            'amount' => 2500,
            'transaction_date' => '2026-04-15',
        ]);

        $response = $this->actingAs($user)->get('/reports/export/excel?from=2026-04-01&to=2026-04-30');

        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
    }
}
