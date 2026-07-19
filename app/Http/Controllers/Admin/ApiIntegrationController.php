<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiIntegrationController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.api-integrations.index', [
            'clients' => ApiClient::query()->latest()->paginate(10),
            'demoToken' => (string) config('salespro.api_token', env('SALES_PRO_API_TOKEN', 'salespro-demo-token')),
            'baseUrl' => $request->getSchemeAndHttpHost(),
        ]);
    }
}
