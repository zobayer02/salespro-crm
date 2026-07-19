<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_manage_customers(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/admin/customers')
            ->assertOk();

        $createResponse = $this->actingAs($owner)
            ->post('/admin/customers', [
                'name' => 'Test Customer',
                'email' => 'customer@salespro.test',
                'phone' => '+8801700000000',
                'address' => 'Dhanmondi, Dhaka',
            ]);

        $customer = Customer::query()->where('email', 'customer@salespro.test')->firstOrFail();

        $createResponse->assertRedirect(route('admin.customers.index'));
        $this->assertSame('Test Customer', $customer->name);

        $this->actingAs($owner)
            ->put(route('admin.customers.update', $customer), [
                'name' => 'Updated Customer',
                'email' => 'customer@salespro.test',
                'phone' => '+8801711111111',
                'address' => 'Uttara, Dhaka',
            ])
            ->assertRedirect(route('admin.customers.index'));

        $customer->refresh();

        $this->assertSame('Updated Customer', $customer->name);
        $this->assertSame('Uttara, Dhaka', $customer->address);

        $this->actingAs($owner)
            ->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseMissing('customers', [
            'email' => 'customer@salespro.test',
        ]);
    }

    public function test_customer_email_must_be_unique(): void
    {
        Customer::query()->create([
            'name' => 'Existing Customer',
            'email' => 'duplicate-customer@salespro.test',
            'phone' => '+8801700000001',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->owner())
            ->post('/admin/customers', [
                'name' => 'Duplicate Customer',
                'email' => 'duplicate-customer@salespro.test',
                'phone' => '+8801700000002',
                'address' => 'Dhaka',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_customer_purchase_history_and_lost_detection_are_automatic(): void
    {
        config(['salespro.lost_customer_days' => 90]);

        $owner = $this->owner();
        $activeCustomer = Customer::query()->create([
            'name' => 'Active Customer',
            'email' => 'active-history@salespro.test',
            'phone' => '+8801700000003',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
        ]);
        $lostCustomer = Customer::query()->create([
            'name' => 'Lost Customer',
            'email' => 'lost-history@salespro.test',
            'phone' => '+8801700000004',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
        ]);
        $product = Product::query()->create([
            'name' => 'History Product',
            'sku' => 'HISTORY-001',
            'price' => 1000,
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);
        $recentSale = Sale::query()->create([
            'order_number' => 'ORD-HISTORY-RECENT',
            'customer_id' => $activeCustomer->id,
            'total_amount' => 2000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now()->subDays(20),
        ]);
        SaleItem::query()->create([
            'sale_id' => $recentSale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 1000,
            'subtotal' => 2000,
        ]);
        Sale::query()->create([
            'order_number' => 'ORD-HISTORY-OLD',
            'customer_id' => $lostCustomer->id,
            'total_amount' => 1000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now()->subDays(120),
        ]);

        $this->actingAs($owner)
            ->get(route('admin.customers.show', $activeCustomer))
            ->assertOk()
            ->assertSeeText('Purchase Frequency')
            ->assertSeeText('Tk 2,000.00')
            ->assertSeeText('Active');

        $this->actingAs($owner)
            ->get(route('admin.customers.inactive'))
            ->assertOk()
            ->assertSeeText('Lost Customer')
            ->assertDontSeeText('Active Customer');
    }

    public function test_non_owner_cannot_manage_customers(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-customers@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employee)
            ->get('/admin/customers')
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
