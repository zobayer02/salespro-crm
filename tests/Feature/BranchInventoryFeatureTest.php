<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Customer;
use App\Models\InvoiceEmailLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchInventoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_manage_branches(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->post(route('admin.branches.store'), [
                'name' => 'Bashundhara Branch',
                'code' => 'BSD',
                'address' => 'Bashundhara, Dhaka',
                'status' => Branch::STATUS_ACTIVE,
            ])
            ->assertRedirect(route('admin.branches.index'));

        $branch = Branch::query()->where('code', 'BSD')->firstOrFail();

        $this->actingAs($owner)
            ->get(route('admin.branches.index'))
            ->assertOk()
            ->assertSee('Bashundhara Branch');

        $this->actingAs($owner)
            ->put(route('admin.branches.update', $branch), [
                'name' => 'Bashundhara City Branch',
                'code' => 'BSD',
                'address' => 'Panthapath, Dhaka',
                'status' => Branch::STATUS_ACTIVE,
            ])
            ->assertRedirect(route('admin.branches.index'));

        $this->assertDatabaseHas('branches', [
            'code' => 'BSD',
            'name' => 'Bashundhara City Branch',
        ]);
    }

    public function test_owner_can_view_branch_inventory_and_invoice_logs(): void
    {
        $owner = $this->owner();
        $branch = $this->branch();
        $customer = $this->customer();
        $product = $this->product();

        BranchInventory::query()->create([
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock_quantity' => 8,
        ]);

        $sale = Sale::query()->create([
            'order_number' => 'ORD-INVOICE-001',
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'total_amount' => 2500,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now(),
        ]);

        InvoiceEmailLog::query()->create([
            'sale_id' => $sale->id,
            'recipient_email' => $customer->email,
            'status' => InvoiceEmailLog::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $this->actingAs($owner)
            ->get(route('admin.inventory.index'))
            ->assertOk()
            ->assertSee($branch->name)
            ->assertSee($product->sku);

        $this->actingAs($owner)
            ->get(route('admin.invoices.index'))
            ->assertOk()
            ->assertSee('ORD-INVOICE-001')
            ->assertSee($customer->email);
    }

    public function test_non_owner_cannot_access_branch_inventory_pages(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-branch@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employee)->get(route('admin.branches.index'))->assertForbidden();
        $this->actingAs($employee)->get(route('admin.inventory.index'))->assertForbidden();
        $this->actingAs($employee)->get(route('admin.invoices.index'))->assertForbidden();
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

    private function branch(): Branch
    {
        return Branch::query()->create([
            'name' => fake()->unique()->city() . ' Branch',
            'code' => fake()->unique()->bothify('BR-##'),
            'address' => 'Dhaka',
            'status' => Branch::STATUS_ACTIVE,
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

    private function product(): Product
    {
        return Product::query()->create([
            'name' => fake()->unique()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'price' => 2500,
            'stock_quantity' => 8,
            'status' => Product::STATUS_ACTIVE,
        ]);
    }
}
