<?php

namespace Tests\Feature;

use App\Mail\CustomerReengagementMail;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\Employee;
use App\Models\ReengagementLog;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReengagementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_send_reengagement_email_to_inactive_customer(): void
    {
        Mail::fake();

        $owner = $this->owner();
        $customer = $this->customer();

        $this->actingAs($owner)
            ->post('/admin/reengagements', [
                'customer_id' => $customer->id,
                'message' => 'Please come back for a special offer.',
            ])
            ->assertSessionHasNoErrors();

        Mail::assertSent(CustomerReengagementMail::class, fn (CustomerReengagementMail $mail) => $mail->hasTo($customer->email));
        $this->assertDatabaseHas('reengagement_logs', [
            'customer_id' => $customer->id,
            'channel' => ReengagementLog::CHANNEL_EMAIL,
            'status' => ReengagementLog::STATUS_SENT,
            'message' => 'Please come back for a special offer.',
        ]);
    }

    public function test_active_customer_cannot_receive_reengagement_email(): void
    {
        Mail::fake();

        $owner = $this->owner();
        $customer = $this->customer();

        Sale::query()->create([
            'order_number' => 'ORD-REENGAGE-ACTIVE',
            'customer_id' => $customer->id,
            'total_amount' => 1000,
            'status' => Sale::STATUS_COMPLETED,
            'sold_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post('/admin/reengagements', [
                'customer_id' => $customer->id,
            ])
            ->assertSessionHasErrors('customer_id');

        Mail::assertNothingSent();
        $this->assertDatabaseCount('reengagement_logs', 0);
    }

    public function test_reengagement_email_can_be_linked_to_assignment(): void
    {
        Mail::fake();

        $owner = $this->owner();
        $customer = $this->customer();
        $employee = Employee::query()->create([
            'name' => 'CRM Agent',
            'email' => 'agent@salespro.test',
            'phone' => '+8801800000001',
            'designation' => 'CRM Executive',
            'kpi_score' => 0,
            'status' => Employee::STATUS_ACTIVE,
        ]);
        $assignment = CustomerAssignment::query()->create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'status' => CustomerAssignment::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        $this->actingAs($owner)
            ->post('/admin/reengagements', [
                'customer_id' => $customer->id,
                'customer_assignment_id' => $assignment->id,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('reengagement_logs', [
            'customer_id' => $customer->id,
            'customer_assignment_id' => $assignment->id,
            'status' => ReengagementLog::STATUS_SENT,
        ]);
    }

    public function test_non_owner_cannot_access_reengagement_logs(): void
    {
        $employeeUser = User::query()->create([
            'name' => 'Employee User',
            'email' => 'employee-reengagements@salespro.test',
            'role' => User::ROLE_EMPLOYEE,
            'password' => 'password',
        ]);

        $this->actingAs($employeeUser)
            ->get('/admin/reengagements')
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
}
