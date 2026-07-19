<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['John Doe', 'john.doe@salespro.test', '+8801711000001', 'Dhanmondi, Dhaka', Customer::STATUS_ACTIVE],
            ['Sarah Khan', 'sarah.khan@salespro.test', '+8801711000002', 'Uttara, Dhaka', Customer::STATUS_ACTIVE],
            ['Abdullah Al Mamun', 'mamun@salespro.test', '+8801711000003', 'Mirpur, Dhaka', Customer::STATUS_ACTIVE],
            ['Nusrat Jahan', 'nusrat.jahan@salespro.test', '+8801711000004', 'Chattogram', Customer::STATUS_ACTIVE],
            ['Rashed Ahmed', 'rashed.ahmed@salespro.test', '+8801711000005', 'Banani, Dhaka', Customer::STATUS_ACTIVE],
            ['Michael Smith', 'michael.smith@salespro.test', '+8801711000006', 'Gulshan, Dhaka', Customer::STATUS_INACTIVE],
            ['Emily Johnson', 'emily.johnson@salespro.test', '+8801711000007', 'Sylhet', Customer::STATUS_INACTIVE],
            ['David Brown', 'david.brown@salespro.test', '+8801711000008', 'Khulna', Customer::STATUS_INACTIVE],
            ['Jessica Wilson', 'jessica.wilson@salespro.test', '+8801711000009', 'Rajshahi', Customer::STATUS_INACTIVE],
            ['Daniel Martinez', 'daniel.martinez@salespro.test', '+8801711000010', 'Barishal', Customer::STATUS_INACTIVE],
        ];

        foreach ($customers as [$name, $email, $phone, $address, $status]) {
            Customer::query()->updateOrCreate([
                'email' => $email,
            ], [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'status' => $status,
            ]);
        }
    }
}
