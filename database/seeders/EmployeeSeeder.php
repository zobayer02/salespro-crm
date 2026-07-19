<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['Tanvir Hasan', 'tanvir.hasan@salespro.test', '+8801811000001', 'Sales Executive', Employee::STATUS_ACTIVE],
            ['Farhana Akter', 'farhana.akter@salespro.test', '+8801811000002', 'CRM Specialist', Employee::STATUS_ACTIVE],
            ['Mahmud Rahman', 'mahmud.rahman@salespro.test', '+8801811000003', 'Inventory Manager', Employee::STATUS_ACTIVE],
            ['Sadia Islam', 'sadia.islam@salespro.test', '+8801811000004', 'Customer Success Officer', Employee::STATUS_ACTIVE],
            ['Imran Hossain', 'imran.hossain@salespro.test', '+8801811000005', 'Sales Coordinator', Employee::STATUS_ACTIVE],
            ['Nabila Chowdhury', 'nabila.chowdhury@salespro.test', '+8801811000006', 'Follow-up Agent', Employee::STATUS_ACTIVE],
        ];

        foreach ($employees as [$name, $email, $phone, $designation, $status]) {
            Employee::query()->updateOrCreate([
                'email' => $email,
            ], [
                'name' => $name,
                'phone' => $phone,
                'designation' => $designation,
                'kpi_score' => 0,
                'status' => $status,
            ]);
        }
    }
}
