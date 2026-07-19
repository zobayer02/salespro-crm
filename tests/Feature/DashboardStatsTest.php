<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_are_calculated_from_database(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Admin',
            'email' => 'owner-dashboard@salespro.test',
            'role' => User::ROLE_OWNER,
            'password' => 'password',
        ]);

        Product::query()->create([
            'name' => 'New Product',
            'sku' => 'NEW-001',
            'price' => 1000,
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $oldProduct = Product::query()->create([
            'name' => 'Old Product',
            'sku' => 'OLD-001',
            'price' => 1000,
            'stock_quantity' => 10,
            'status' => Product::STATUS_ACTIVE,
        ]);
        $oldProduct->forceFill([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ])->save();

        $customer = Customer::query()->create([
            'name' => 'Active Customer',
            'email' => 'active-dashboard@salespro.test',
            'phone' => '+8801700000001',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $inactiveCustomer = Customer::query()->create([
            'name' => 'Inactive Customer',
            'email' => 'inactive-dashboard@salespro.test',
            'phone' => '+8801700000002',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_INACTIVE,
        ]);
        $inactiveCustomer->forceFill([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ])->save();

        Sale::query()->create([
            'order_number' => 'ORD-TEST-NEW',
            'customer_id' => $customer->id,
            'total_amount' => 3000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now(),
        ]);
        Sale::query()->create([
            'order_number' => 'ORD-TEST-OLD',
            'customer_id' => $customer->id,
            'total_amount' => 2000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now()->subDays(10),
        ]);

        $this->actingAs($owner)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSeeText('Tk 5,000.00')
            ->assertSeeText('Tk 3,000.00 last 7 days')
            ->assertSeeText('1 orders last 7 days')
            ->assertDontSeeText('Recorded sales value')
            ->assertDontSeeText('Needs re-engagement')
            ->assertSeeText('+1 new this week')
            ->assertSeeText('1 need follow-up');
    }

    public function test_sales_by_branch_filter_uses_selected_range(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Admin',
            'email' => 'owner-branch-dashboard@salespro.test',
            'role' => User::ROLE_OWNER,
            'password' => 'password',
        ]);
        $customer = Customer::query()->create([
            'name' => 'Branch Customer',
            'email' => 'branch-customer@salespro.test',
            'phone' => '+8801700000003',
            'address' => 'Dhaka',
            'status' => Customer::STATUS_ACTIVE,
        ]);
        $currentBranch = Branch::query()->create([
            'name' => 'Current Branch',
            'code' => 'CUR',
            'status' => Branch::STATUS_ACTIVE,
        ]);
        $oldBranch = Branch::query()->create([
            'name' => 'Old Branch',
            'code' => 'OLD',
            'status' => Branch::STATUS_ACTIVE,
        ]);

        Sale::query()->create([
            'order_number' => 'ORD-BRANCH-CURRENT',
            'customer_id' => $customer->id,
            'branch_id' => $currentBranch->id,
            'total_amount' => 3000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now(),
        ]);
        Sale::query()->create([
            'order_number' => 'ORD-BRANCH-OLD',
            'customer_id' => $customer->id,
            'branch_id' => $oldBranch->id,
            'total_amount' => 2000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now()->subMonth(),
        ]);

        $this->actingAs($owner)
            ->get('/admin/dashboard?branch_sales_range=this_month')
            ->assertOk()
            ->assertSeeText('Current Branch')
            ->assertSeeText('100% (Tk 3,000.00)')
            ->assertDontSeeText('Tk 2,000.00');

        $this->actingAs($owner)
            ->get('/admin/dashboard?branch_sales_range=all_time')
            ->assertOk()
            ->assertSeeText('60% (Tk 3,000.00)')
            ->assertSeeText('40% (Tk 2,000.00)');
    }
}
