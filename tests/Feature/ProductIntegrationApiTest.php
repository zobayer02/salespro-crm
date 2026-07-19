<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIntegrationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_api_requires_valid_bearer_token(): void
    {
        $this->getJson('/api/v1/products')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');

        $this->getJson('/api/v1/products', [
            'Authorization' => 'Bearer invalid-token',
        ])->assertUnauthorized();
    }

    public function test_product_api_returns_active_products_for_valid_client(): void
    {
        $token = $this->apiToken();

        Product::query()->create([
            'name' => 'iPhone 15 Pro',
            'sku' => 'IP15PRO',
            'price' => 135000,
            'stock_quantity' => 24,
            'status' => Product::STATUS_ACTIVE,
        ]);

        Product::query()->create([
            'name' => 'Inactive Product',
            'sku' => 'INACTIVE-001',
            'price' => 1000,
            'stock_quantity' => 10,
            'status' => Product::STATUS_INACTIVE,
        ]);

        $this->getJson('/api/v1/products', [
            'Authorization' => "Bearer {$token}",
        ])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.sku', 'IP15PRO')
            ->assertJsonPath('data.0.product_name', 'iPhone 15 Pro')
            ->assertJsonPath('data.0.price', 135000)
            ->assertJsonPath('data.0.available_stock', 24)
            ->assertJsonMissingPath('data.0.id')
            ->assertJsonMissingPath('data.0.status');
    }

    public function test_product_api_can_filter_by_search_and_sku(): void
    {
        $token = $this->apiToken();

        Product::query()->create([
            'name' => 'Samsung Galaxy S24',
            'sku' => 'SG24',
            'price' => 118000,
            'stock_quantity' => 32,
            'status' => Product::STATUS_ACTIVE,
        ]);

        Product::query()->create([
            'name' => 'Sony WH-1000XM5',
            'sku' => 'SONY1000XM5',
            'price' => 36000,
            'stock_quantity' => 42,
            'status' => Product::STATUS_ACTIVE,
        ]);

        $this->getJson('/api/v1/products?search=sony', [
            'Authorization' => "Bearer {$token}",
        ])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.sku', 'SONY1000XM5');

        $this->getJson('/api/v1/products?sku=SG24', [
            'Authorization' => "Bearer {$token}",
        ])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.product_name', 'Samsung Galaxy S24');
    }

    public function test_product_api_can_show_single_active_product_by_sku(): void
    {
        $token = $this->apiToken();

        Product::query()->create([
            'name' => 'Dell Inspiron 15',
            'sku' => 'DELL15',
            'price' => 78000,
            'stock_quantity' => 18,
            'status' => Product::STATUS_ACTIVE,
        ]);

        Product::query()->create([
            'name' => 'Inactive Product',
            'sku' => 'INACTIVE-002',
            'price' => 1000,
            'stock_quantity' => 10,
            'status' => Product::STATUS_INACTIVE,
        ]);

        $this->getJson('/api/v1/products/DELL15', [
            'Authorization' => "Bearer {$token}",
        ])
            ->assertOk()
            ->assertJsonPath('data.sku', 'DELL15')
            ->assertJsonPath('data.available_stock', 18);

        $this->getJson('/api/v1/products/INACTIVE-002', [
            'Authorization' => "Bearer {$token}",
        ])->assertNotFound();
    }

    public function test_api_client_last_used_time_is_updated(): void
    {
        $token = 'client-token';
        $client = ApiClient::query()->create([
            'name' => 'Test Client',
            'token_hash' => ApiClient::hashToken($token),
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/products', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk();

        $this->assertNotNull($client->refresh()->last_used_at);
    }

    public function test_owner_can_view_api_integrations_page(): void
    {
        ApiClient::query()->create([
            'name' => 'Demo E-commerce Client',
            'token_hash' => ApiClient::hashToken('demo-token'),
            'is_active' => true,
        ]);

        $owner = User::query()->create([
            'name' => 'Owner Admin',
            'email' => 'owner-api-page@salespro.test',
            'role' => User::ROLE_OWNER,
            'password' => 'password',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.api-integrations.index'))
            ->assertOk()
            ->assertSee('E-commerce Integration API')
            ->assertSee('Demo E-commerce Client')
            ->assertSee('/api/v1/products');
    }

    private function apiToken(): string
    {
        $token = 'test-api-token';

        ApiClient::query()->create([
            'name' => 'Test E-commerce Client',
            'token_hash' => ApiClient::hashToken($token),
            'is_active' => true,
        ]);

        return $token;
    }
}
