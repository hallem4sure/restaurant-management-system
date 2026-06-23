<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\MenuCategory;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    private function validPayload($menuItemId, array $overrides = []): array
    {
        return array_merge([
            'type'   => 'walk_in',
            'status' => 'pending',
            'items'  => [
                [
                    'menu_item_id' => $menuItemId,
                    'quantity'     => 2,
                ]
            ]
        ], $overrides);
    }
    
    private function createMenuItem()
    {
        $section = MenuSection::create(['name' => 'Main', 'is_active' => true]);
        $category = MenuCategory::create(['section_id' => $section->id, 'name' => 'Burgers', 'is_active' => true]);

        return MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Burger',
            'price' => 10.00,
            'is_available' => 1,
        ]);
    }

    /** @test */
    public function waiter_can_create_order()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $menuItem = $this->createMenuItem();

        $response = $this->actingAs($waiter)->post('/admin/orders', $this->validPayload($menuItem->id));

        $response->assertRedirect('/admin/orders');
        $this->assertDatabaseHas('orders', ['type' => 'walk_in', 'subtotal' => 20.00]);
        $this->assertDatabaseHas('order_items', ['menu_item_id' => $menuItem->id, 'quantity' => 2]);
    }

    /** @test */
    public function order_fails_without_items()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/orders', [
            'type'   => 'walk_in',
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('orders', 0);
    }

    /** @test */
    public function admin_can_update_order()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $menuItem = $this->createMenuItem();

        $this->actingAs($admin)->post('/admin/orders', $this->validPayload($menuItem->id));
        $order = Order::first();

        $response = $this->actingAs($admin)->put("/admin/orders/{$order->id}", $this->validPayload($menuItem->id, [
            'status' => 'preparing'
        ]));

        $response->assertRedirect('/admin/orders');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'preparing']);
    }

    /** @test */
    public function waiter_cannot_delete_order()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $menuItem = $this->createMenuItem();

        // Setup an order first
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin)->post('/admin/orders', $this->validPayload($menuItem->id));
        $order = Order::first();

        // Attempt delete as waiter
        $response = $this->actingAs($waiter)->delete("/admin/orders/{$order->id}");

        $response->assertForbidden();
    }
}
