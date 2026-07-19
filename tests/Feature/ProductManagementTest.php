<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_manage_products(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/admin/products')
            ->assertOk();

        $createResponse = $this->actingAs($owner)
            ->post('/admin/products', [
                'name' => 'Test Product',
                'sku' => 'TEST-001',
                'price' => 1500,
                'stock_quantity' => 20,
                'status' => Product::STATUS_ACTIVE,
            ]);

        $product = Product::query()->where('sku', 'TEST-001')->firstOrFail();

        $createResponse->assertRedirect(route('admin.products.index'));
        $this->assertSame('Test Product', $product->name);

        $this->actingAs($owner)
            ->put(route('admin.products.update', $product), [
                'name' => 'Updated Product',
                'sku' => 'TEST-001',
                'price' => 1750,
                'stock_quantity' => 12,
                'status' => Product::STATUS_INACTIVE,
            ])
            ->assertRedirect(route('admin.products.index'));

        $product->refresh();

        $this->assertSame('Updated Product', $product->name);
        $this->assertSame(Product::STATUS_INACTIVE, $product->status);
        $this->assertSame(12, $product->stock_quantity);

        $this->actingAs($owner)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', [
            'sku' => 'TEST-001',
        ]);
    }

    public function test_product_sku_must_be_unique(): void
    {
        Product::query()->create([
            'name' => 'Existing Product',
            'sku' => 'DUP-001',
            'price' => 1000,
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->owner())
            ->post('/admin/products', [
                'name' => 'Duplicate Product',
                'sku' => 'DUP-001',
                'price' => 1000,
                'stock_quantity' => 10,
                'status' => Product::STATUS_ACTIVE,
            ])
            ->assertSessionHasErrors('sku');
    }

    public function test_non_owner_cannot_manage_products(): void
    {
        $employee = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-products@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employee)
            ->get('/admin/products')
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
