<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['Dhanmondi Branch', 'DHA', 'Dhanmondi, Dhaka'],
            ['Uttara Branch', 'UTR', 'Uttara, Dhaka'],
            ['Mirpur Branch', 'MIR', 'Mirpur, Dhaka'],
            ['Chattogram Branch', 'CTG', 'Chattogram'],
        ];

        foreach ($branches as [$name, $code, $address]) {
            Branch::query()->updateOrCreate([
                'code' => $code,
            ], [
                'name' => $name,
                'address' => $address,
                'status' => Branch::STATUS_ACTIVE,
            ]);
        }
    }
}
