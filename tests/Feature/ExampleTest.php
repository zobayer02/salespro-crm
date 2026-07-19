<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_owner_can_login_and_access_dashboard(): void
    {
        User::query()->create([
            'name' => 'Owner Admin',
            'email' => 'zobayer1084@gmail.com',
            'role' => 'owner',
            'password' => '@password/',
        ]);

        $response = $this->post('/login', [
            'email' => 'zobayer1084@gmail.com',
            'password' => '@password/',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->get('/dashboard')->assertRedirect('/admin/dashboard');
        $this->get('/admin/dashboard')->assertOk();
    }

    public function test_non_owner_cannot_access_admin_dashboard(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employee)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }
}
