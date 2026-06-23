<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\RestaurantTable;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    private function validPayload($tableId, array $overrides = []): array
    {
        return array_merge([
            'table_id'         => $tableId,
            'customer_name'    => 'Test Customer',
            'party_size'       => 2,
            'type'             => 'scheduled',
            'reserved_at'      => now()->addDay()->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
            'status'           => 'pending',
        ], $overrides);
    }

    /** @test */
    public function admin_can_create_reservation()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $table = RestaurantTable::create([
            'table_number' => 'T1',
            'capacity'     => 4,
            'status'       => 'available'
        ]);

        $response = $this->actingAs($admin)->post('/admin/reservations', $this->validPayload($table->id));

        $response->assertRedirect('/admin/reservations');
        $this->assertDatabaseHas('reservations', ['customer_name' => 'Test Customer']);
    }

    /** @test */
    public function reservation_fails_if_party_size_exceeds_capacity()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $table = RestaurantTable::create([
            'table_number' => 'T1',
            'capacity'     => 2,
            'status'       => 'available'
        ]);

        $response = $this->actingAs($admin)->post('/admin/reservations', $this->validPayload($table->id, [
            'party_size' => 4,
        ]));

        $response->assertSessionHasErrors('party_size');
        $this->assertDatabaseCount('reservations', 0);
    }

    /** @test */
    public function reservation_fails_if_table_is_already_booked_at_requested_time()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $table = RestaurantTable::create([
            'table_number' => 'T1',
            'capacity'     => 4,
            'status'       => 'available'
        ]);

        $time = Carbon::now()->addDay()->setHour(19)->setMinute(0)->setSecond(0);

        // First reservation 19:00 - 20:00
        Reservation::create($this->validPayload($table->id, [
            'reserved_at'      => $time->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'status'           => 'confirmed',
        ]));

        // Second reservation 19:30 - 20:30 (overlaps)
        $response = $this->actingAs($admin)->post('/admin/reservations', $this->validPayload($table->id, [
            'reserved_at'      => $time->copy()->addMinutes(30)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ]));

        $response->assertSessionHasErrors('reserved_at');
        $this->assertDatabaseCount('reservations', 1);
    }

    /** @test */
    public function admin_can_update_reservation_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $table = RestaurantTable::create([
            'table_number' => 'T1',
            'capacity'     => 4,
            'status'       => 'available'
        ]);

        $reservation = Reservation::create($this->validPayload($table->id));

        $response = $this->actingAs($admin)->patch("/admin/reservations/{$reservation->id}/status", [
            'status' => 'seated'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'seated']);
        $this->assertDatabaseHas('restaurant_tables', ['id' => $table->id, 'status' => 'occupied']);
    }

    /** @test */
    public function waiter_cannot_delete_reservation()
    {
        $waiter = User::factory()->create();
        $waiter->assignRole('waiter');

        $table = RestaurantTable::create([
            'table_number' => 'T1',
            'capacity'     => 4,
            'status'       => 'available'
        ]);

        $reservation = Reservation::create($this->validPayload($table->id));

        $response = $this->actingAs($waiter)->delete("/admin/reservations/{$reservation->id}");

        $response->assertForbidden();
    }
}
