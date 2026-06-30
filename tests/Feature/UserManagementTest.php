<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Reservation;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    private function createAdmin()
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('admin');
        return $admin;
    }

    #[Test]
    public function admin_can_view_users_index()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertOk();
    }

    #[Test]
    public function non_admin_cannot_view_users_index()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $response = $this->actingAs($waiter)->get('/admin/users');
        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_create_user()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Staff',
            'email' => 'new@restaurant.com',
            'password' => 'password123',
            'role' => 'waiter'
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'new@restaurant.com']);
        
        $newUser = User::where('email', 'new@restaurant.com')->first();
        $this->assertTrue($newUser->hasRole('waiter'));
    }

    #[Test]
    public function admin_can_update_user()
    {
        $admin = $this->createAdmin();
        $staff = User::factory()->create(['email' => 'staff@rest.com']);
        $staff->assignRole('waiter');

        $response = $this->actingAs($admin)->put("/admin/users/{$staff->id}", [
            'name' => 'Updated Name',
            'email' => 'staff@rest.com',
            'role' => 'cashier'
        ]);

        $response->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['id' => $staff->id, 'name' => 'Updated Name']);
        $this->assertTrue($staff->fresh()->hasRole('cashier'));
    }

    #[Test]
    public function admin_can_deactivate_user()
    {
        $admin = $this->createAdmin();
        $staff = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patch("/admin/users/{$staff->id}/toggle-status");
        
        $response->assertRedirect();
        $this->assertFalse((bool)$staff->fresh()->is_active);
    }

    #[Test]
    public function admin_cannot_deactivate_self()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->patch("/admin/users/{$admin->id}/toggle-status");
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertTrue((bool)$admin->fresh()->is_active);
    }

    #[Test]
    public function admin_can_reset_password()
    {
        $admin = $this->createAdmin();
        $staff = User::factory()->create(['password' => Hash::make('old_password')]);

        $response = $this->actingAs($admin)->patch("/admin/users/{$staff->id}/reset-password", [
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('new_password123', $staff->fresh()->password));
    }

    #[Test]
    public function admin_can_delete_user_without_relations()
    {
        $admin = $this->createAdmin();
        $staff = User::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/users/{$staff->id}");
        
        $response->assertRedirect();
        $this->assertSoftDeleted('users', ['id' => $staff->id]);
    }

    #[Test]
    public function admin_cannot_delete_user_with_relations()
    {
        $admin = $this->createAdmin();
        $staff = User::factory()->create();
        
        $table = \App\Models\RestaurantTable::create(['table_number' => 1, 'capacity' => 4, 'status' => 'available']);

        // Create relationship
        \App\Models\Order::create([
            'order_number' => 'TEST-123',
            'type' => 'walk_in',
            'status' => 'pending',
            'subtotal' => 0,
            'tax_amount' => 0,
            'service_charge_amount' => 0,
            'total_amount' => 0,
            'waiter_id' => $staff->id
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$staff->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $staff->id, 'deleted_at' => null]);
    }

    #[Test]
    public function admin_cannot_delete_self()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }
}
