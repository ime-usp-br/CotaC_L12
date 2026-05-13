<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class AdminAccessTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    #[Group('dusk')]
    #[Group('admin')]
    public function test_admin_pode_acessar_recursos(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create([
                'email' => 'admin-dusk@example.com',
                'password' => 'password',
            ]);
            $role = Role::create(['name' => 'Admin']);
            $user->assignRole($role);

            $browser->visit('/login/local')
                ->waitFor('@email-input')
                ->type('@email-input', 'admin-dusk@example.com')
                ->type('@password-input', 'password')
                ->click('@login-button')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard');

            $browser->visit('/admin')
                ->waitForText('Dashboard')
                ->assertPathIs('/admin')
                ->assertSee('Produtos')
                ->assertSee('Cotas');
        });
    }
}
