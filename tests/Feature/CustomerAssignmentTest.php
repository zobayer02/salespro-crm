<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\CustomerAssignment;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_assign_lost_customer_to_employee(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $employee = $this->employee();

        $this->actingAs($owner)
            ->get('/admin/assignments/create')
            ->assertOk()
            ->assertSeeText($customer->name)
            ->assertSeeText($employee->name);

        $this->actingAs($owner)
            ->post('/admin/assignments', [
                'customer_id' => $customer->id,
                'employee_id' => $employee->id,
            ])
            ->assertRedirect(route('admin.assignments.index'));

        $this->assertDatabaseHas('customer_assignments', [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'status' => CustomerAssignment::STATUS_ASSIGNED,
        ]);
    }

    public function test_active_customer_cannot_be_assigned(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $employee = $this->employee();

        Sale::query()->create([
            'order_number' => 'ORD-ACTIVE-CUSTOMER',
            'customer_id' => $customer->id,
            'total_amount' => 1000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post('/admin/assignments', [
                'customer_id' => $customer->id,
                'employee_id' => $employee->id,
            ])
            ->assertSessionHasErrors('customer_id');

        $this->assertDatabaseCount('customer_assignments', 0);
    }

    public function test_duplicate_active_assignment_is_prevented(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $employee = $this->employee();

        CustomerAssignment::query()->create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'status' => CustomerAssignment::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post('/admin/assignments', [
                'customer_id' => $customer->id,
                'employee_id' => $employee->id,
            ])
            ->assertSessionHasErrors('customer_id');

        $this->assertDatabaseCount('customer_assignments', 1);
    }

    public function test_assigned_customer_purchase_converts_assignment_and_increases_employee_kpi(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $employee = $this->employee();
        $product = Product::query()->create([
            'name' => 'KPI Product',
            'sku' => 'KPI-001',
            'price' => 2500,
            'stock_quantity' => 5,
            'status' => Product::STATUS_ACTIVE,
        ]);
        $branch = $this->branchWithInventory($product, 5);

        $assignment = CustomerAssignment::query()->create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'status' => CustomerAssignment::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post('/admin/sales', [
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ])
            ->assertRedirect();

        $assignment->refresh();
        $employee->refresh();

        $this->assertSame(CustomerAssignment::STATUS_CONVERTED, $assignment->status);
        $this->assertNotNull($assignment->converted_at);
        $this->assertSame(1, $employee->kpi_score);

        $this->actingAs($owner)
            ->post('/admin/sales', [
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ])
            ->assertRedirect();

        $this->assertSame(1, $employee->refresh()->kpi_score);
    }

    public function test_non_owner_cannot_manage_assignments(): void
    {
        $employeeUser = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-assignments@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employeeUser)
            ->get('/admin/assignments')
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

    private function customer(): Customer
    {
        return Customer::query()->create([
            'name' => fake()->unique()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+8801700000000',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
        ]);
    }

    private function employee(): Employee
    {
        return Employee::query()->create([
            'name' => fake()->unique()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+8801800000000',
            'designation' => 'CRM Executive',
            'kpi_score' => 0,
            'status' => Employee::STATUS_ACTIVE,
        ]);
    }

    private function branchWithInventory(Product $product, int $stockQuantity): Branch
    {
        $branch = Branch::query()->create([
            'name' => fake()->unique()->city() . ' Branch',
            'code' => fake()->unique()->bothify('BR-##'),
            'address' => 'Dhaka',
            'status' => Branch::STATUS_ACTIVE,
        ]);

        BranchInventory::query()->create([
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock_quantity' => $stockQuantity,
        ]);

        return $branch;
    }
}
