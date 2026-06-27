<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\MenuSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KitchenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    #[Test]
    public function kitchen_staff_can_view_kitchen_dashboard()
    {
        $kitchenStaff = User::factory()->create();
        $kitchenStaff->assignRole('kitchen_staff');

        $response = $this->actingAs($kitchenStaff)->get('/admin/kitchen');

        $response->assertStatus(200);
        $response->assertViewIs('admin.kitchen.index');
    }

    #[Test]
    public function normal_user_cannot_view_kitchen_dashboard()
    {
        $user = User::factory()->create();
        // user has no roles/permissions

        $response = $this->actingAs($user)->get('/admin/kitchen');

        $response->assertForbidden();
    }

    #[Test]
    public function kitchen_staff_can_update_item_status()
    {
        $kitchenStaff = User::factory()->create();
        $kitchenStaff->assignRole('kitchen_staff');

        $section = MenuSection::create(['name' => 'Test Section']);
        $category = MenuCategory::create(['name' => 'Test Category', 'section_id' => $section->id]);
        $menuItem = MenuItem::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'price' => 10.00,
            'is_active' => true
        ]);

        $order = Order::create([
            'order_number' => 'TEST-123',
            'type' => 'walk_in',
            'status' => 'preparing',
            'subtotal' => 10,
            'tax_amount' => 0,
            'service_charge_amount' => 0,
            'total_amount' => 10
        ]);

        $item = $order->items()->create([
            'menu_item_id' => $menuItem->id,
            'quantity' => 1,
            'unit_price' => 10,
            'subtotal' => 10,
            'kitchen_status' => 'pending'
        ]);

        $response = $this->actingAs($kitchenStaff)->patch("/admin/kitchen/item/{$item->id}/status", [
            'status' => 'ready'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'kitchen_status' => 'ready'
        ]);
    }

    #[Test]
    public function order_status_syncs_when_all_items_are_ready()
    {
        $kitchenStaff = User::factory()->create();
        $kitchenStaff->assignRole('kitchen_staff');

        $section = MenuSection::create(['name' => 'Test Section']);
        $category = MenuCategory::create(['name' => 'Test Category', 'section_id' => $section->id]);
        $menuItem = MenuItem::create([
            'name' => 'Test Item',
            'category_id' => $category->id,
            'price' => 10.00,
            'is_active' => true
        ]);

        $order = Order::create([
            'order_number' => 'TEST-123',
            'type' => 'walk_in',
            'status' => 'preparing',
            'subtotal' => 20,
            'tax_amount' => 0,
            'service_charge_amount' => 0,
            'total_amount' => 20
        ]);

        $item1 = $order->items()->create([
            'menu_item_id' => $menuItem->id,
            'quantity' => 1,
            'unit_price' => 10,
            'subtotal' => 10,
            'kitchen_status' => 'ready'
        ]);

        $item2 = $order->items()->create([
            'menu_item_id' => $menuItem->id,
            'quantity' => 1,
            'unit_price' => 10,
            'subtotal' => 10,
            'kitchen_status' => 'pending'
        ]);

        // Order is still preparing
        $this->assertEquals('preparing', $order->fresh()->status);

        // Update item 2 to ready
        $this->actingAs($kitchenStaff)->patch("/admin/kitchen/item/{$item2->id}/status", [
            'status' => 'ready'
        ]);

        // Order should now be ready
        $this->assertEquals('ready', $order->fresh()->status);
    }
}
