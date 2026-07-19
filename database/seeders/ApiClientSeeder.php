<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use Illuminate\Database\Seeder;

class ApiClientSeeder extends Seeder
{
    public function run(): void
    {
        ApiClient::query()->updateOrCreate(
            ['name' => 'Demo E-commerce Client'],
            [
                'token_hash' => ApiClient::hashToken((string) env('SALES_PRO_API_TOKEN', 'salespro-demo-token')),
                'is_active' => true,
            ]
        );
    }
}
