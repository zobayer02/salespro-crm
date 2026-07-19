<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_manage_employees(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/admin/employees')
            ->assertOk();

        $createResponse = $this->actingAs($owner)
            ->post('/admin/employees', [
                'name' => 'Test Employee',
                'email' => 'employee@salespro.test',
                'phone' => '+8801800000000',
                'designation' => 'CRM Executive',
                'status' => Employee::STATUS_ACTIVE,
            ]);

        $employee = Employee::query()->where('email', 'employee@salespro.test')->firstOrFail();

        $createResponse->assertRedirect(route('admin.employees.index'));
        $this->assertSame('Test Employee', $employee->name);
        $this->assertSame(0, $employee->kpi_score);

        $this->actingAs($owner)
            ->put(route('admin.employees.update', $employee), [
                'name' => 'Updated Employee',
                'email' => 'employee@salespro.test',
                'phone' => '+8801811111111',
                'designation' => 'Senior CRM Executive',
                'status' => Employee::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('admin.employees.index'));

        $employee->refresh();

        $this->assertSame('Updated Employee', $employee->name);
        $this->assertSame(0, $employee->kpi_score);
        $this->assertSame(Employee::STATUS_INACTIVE, $employee->status);

        $this->actingAs($owner)
            ->delete(route('admin.employees.destroy', $employee))
            ->assertRedirect(route('admin.employees.index'));

        $this->assertDatabaseMissing('employees', [
            'email' => 'employee@salespro.test',
        ]);
    }

    public function test_employee_email_must_be_unique(): void
    {
        Employee::query()->create([
            'name' => 'Existing Employee',
            'email' => 'duplicate-employee@salespro.test',
            'phone' => '+8801800000001',
            'designation' => 'Sales Executive',
            'kpi_score' => 8,
            'status' => Employee::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->owner())
            ->post('/admin/employees', [
                'name' => 'Duplicate Employee',
                'email' => 'duplicate-employee@salespro.test',
                'phone' => '+8801800000002',
                'designation' => 'Sales Executive',
                'status' => Employee::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_non_owner_cannot_manage_employees(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-employees@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employee)
            ->get('/admin/employees')
            ->assertForbidden();
    }

    private function owner(): User
    {
        return User::query()->create([
            'name' => 'Owner Admin',
            'email' => fake()->unique()->safeEmail(),
            'role' => User::ROLE_OWNER,
            'password' => 'password',
        ]);
    }
}
