<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirects_to_admin_dashboard()
    {
        $this->seed();

        $response = $this->post('/login', [
            'email'    => 'admin@restaurant.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect('admin/dashboard');
    }

    public function test_kitchen_staff_redirects_to_kitchen_dashboard()
    {
        $this->seed();

        $response = $this->post('/login', [
            'email'    => 'kitchen@restaurant.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect('admin/kitchen');
    }

    public function test_invalid_credentials_rejected()
    {
        $this->seed();

        $response = $this->post('/login', [
            'email'    => 'admin@restaurant.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
