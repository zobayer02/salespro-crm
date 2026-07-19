<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\InvoiceEmailLog;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_search_and_filter_invoice_logs(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Admin',
            'email' => 'owner-invoices@salespro.test',
            'role' => User::ROLE_OWNER,
            'password' => 'password',
        ]);

        $branch = Branch::query()->create([
            'name' => 'Dhanmondi Branch',
            'code' => 'DHA',
            'status' => Branch::STATUS_ACTIVE,
        ]);

        $customer = Customer::query()->create([
            'name' => 'Rashed Ahmed',
            'email' => 'rashed.ahmed@salespro.test',
            'status' => Customer::STATUS_ACTIVE,
        ]);

        $sale = Sale::query()->create([
            'order_number' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'total_amount' => 52000,
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
            ->get(route('admin.invoices.index', ['search' => 'Rashed', 'status' => InvoiceEmailLog::STATUS_SENT]))
            ->assertOk()
            ->assertSee('Rashed Ahmed')
            ->assertSee('ORD-TEST-001')
            ->assertSee('Sent')
            ->assertSee('Search order, customer or email')
            ->assertSee('All Status');
    }
}
