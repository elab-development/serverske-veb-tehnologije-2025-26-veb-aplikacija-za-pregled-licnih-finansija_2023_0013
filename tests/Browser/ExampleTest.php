<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    public function test_pocetna_strana_se_otvara(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertTitleContains('Licne Finansije');
        });
    }

    public function test_login_stranica_se_otvara(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Email')
                ->assertSee('Password');
        });
    }
}
