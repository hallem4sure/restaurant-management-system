<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingsSeeder']);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        return $admin;
    }

    #[Test]
    public function admin_can_view_settings_page()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertOk();
        $response->assertViewIs('admin.settings.index');
    }

    #[Test]
    public function non_admin_cannot_view_settings_page()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');
        $response = $this->actingAs($waiter)->get('/admin/settings');
        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_update_settings()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'settings' => [
                'general.restaurant_name' => 'New Restaurant Name',
                'billing.tax_rate'         => '20.00',
                'billing.currency_symbol'  => '€',
            ],
        ]);

        $response->assertRedirect('/admin/settings');

        $this->assertDatabaseHas('settings', [
            'key'   => 'general.restaurant_name',
            'value' => 'New Restaurant Name',
        ]);
        $this->assertDatabaseHas('settings', [
            'key'   => 'billing.tax_rate',
            'value' => '20.00',
        ]);
        $this->assertDatabaseHas('settings', [
            'key'   => 'billing.currency_symbol',
            'value' => '€',
        ]);
    }

    #[Test]
    public function setting_helper_returns_correct_value()
    {
        $admin = $this->createAdmin();

        // Update via controller to also test that helper reads from DB
        $this->actingAs($admin)->post('/admin/settings', [
            'settings' => [
                'billing.currency_symbol' => '£',
            ],
        ]);

        // Clear cache before checking (in tests cache may be warm from seeder)
        \Illuminate\Support\Facades\Cache::flush();

        $this->assertEquals('£', setting('billing.currency_symbol', '$'));
    }

    #[Test]
    public function setting_helper_returns_default_when_key_missing()
    {
        $this->assertEquals('fallback_default', setting('nonexistent.key', 'fallback_default'));
    }

    #[Test]
    public function cashier_cannot_update_settings()
    {
        $cashier = User::factory()->create();
        $cashier->assignRole('cashier');

        $response = $this->actingAs($cashier)->post('/admin/settings', [
            'settings' => ['general.restaurant_name' => 'Hacked Name'],
        ]);

        $response->assertForbidden();
    }
}
