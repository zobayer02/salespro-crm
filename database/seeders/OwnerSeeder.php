<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'owner@salespro.test',
        ], [
            'name' => 'Owner Admin',
            'role' => User::ROLE_OWNER,
            'password' => Hash::make('password'),
        ]);
    }
}
