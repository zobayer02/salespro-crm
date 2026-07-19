<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\InvoiceEmailLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_record_sale_and_stock_is_deducted(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $product = $this->product(stockQuantity: 5, price: 1500);
        $branch = $this->branchWithInventory($product, 5);

        $response = $this->actingAs($owner)
            ->post('/admin/sales', [
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
            ]);

        $sale = Sale::query()->firstOrFail();

        $response->assertRedirect(route('admin.sales.show', $sale));
        $this->assertSame($customer->id, $sale->customer_id);
        $this->assertSame($branch->id, $sale->branch_id);
        $this->assertSame('3000.00', $sale->total_amount);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 1500,
            'subtotal' => 3000,
        ]);
        $this->assertSame(3, $product->refresh()->stock_quantity);
        $this->assertSame(3, BranchInventory::query()->where('branch_id', $branch->id)->where('product_id', $product->id)->value('stock_quantity'));
        $this->assertDatabaseHas('invoice_email_logs', [
            'sale_id' => $sale->id,
            'recipient_email' => $customer->email,
            'status' => InvoiceEmailLog::STATUS_SENT,
        ]);
    }

    public function test_sale_is_prevented_when_stock_is_insufficient(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $product = $this->product(stockQuantity: 1, price: 1500);
        $branch = $this->branchWithInventory($product, 1);

        $this->actingAs($owner)
            ->post('/admin/sales', [
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
            ])
            ->assertSessionHasErrors('items');

        $this->assertSame(1, $product->refresh()->stock_quantity);
        $this->assertSame(1, BranchInventory::query()->where('branch_id', $branch->id)->where('product_id', $product->id)->value('stock_quantity'));
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
    }

    public function test_same_product_rows_are_combined_before_stock_check(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();
        $product = $this->product(stockQuantity: 5, price: 1000);
        $branch = $this->branchWithInventory($product, 5);

        $this->actingAs($owner)
            ->post('/admin/sales', [
                'customer_id' => $customer->id,
                'branch_id' => $branch->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                    ['product_id' => $product->id, 'quantity' => 3],
                ],
            ])
            ->assertRedirect();

        $sale = Sale::query()->firstOrFail();

        $this->assertSame('5000.00', $sale->total_amount);
        $this->assertSame(0, $product->refresh()->stock_quantity);
        $this->assertSame(0, BranchInventory::query()->where('branch_id', $branch->id)->where('product_id', $product->id)->value('stock_quantity'));
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'subtotal' => 5000,
        ]);
    }

    public function test_non_owner_cannot_manage_sales(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-sales@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employee)
            ->get('/admin/sales')
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
            'name' => 'Test Customer',
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+8801700000000',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
        ]);
    }

    private function product(int $stockQuantity, int $price): Product
    {
        return Product::query()->create([
            'name' => fake()->unique()->words(3, true),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'price' => $price,
            'stock_quantity' => $stockQuantity,
            'status' => Product::STATUS_ACTIVE,
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
