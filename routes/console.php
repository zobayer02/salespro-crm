<?php

use App\Models\ApiClient;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('salespro:api-token {name} {--token=}', function (): void {
    $token = (string) ($this->option('token') ?: Str::random(48));

    ApiClient::query()->updateOrCreate(
        ['name' => $this->argument('name')],
        [
            'token_hash' => ApiClient::hashToken($token),
            'is_active' => true,
        ]
    );

    $this->info('API token created.');
    $this->line('Client: '.$this->argument('name'));
    $this->line('Token: '.$token);
})->purpose('Create or rotate a SalesPro integration API token');
