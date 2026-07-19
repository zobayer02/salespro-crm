<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class CustomerAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()
            ->lost(Customer::lostCustomerDays())
            ->take(3)
            ->get();
        $employees = Employee::query()
            ->where('status', Employee::STATUS_ACTIVE)
            ->take(3)
            ->get();

        foreach ($customers as $index => $customer) {
            $employee = $employees->get($index);

            if ($employee) {
                CustomerAssignment::query()->updateOrCreate([
                    'customer_id' => $customer->id,
                    'status' => CustomerAssignment::STATUS_ASSIGNED,
                ], [
                    'employee_id' => $employee->id,
                    'assigned_at' => now()->subDays($index + 1),
                ]);
            }
        }
    }
}
