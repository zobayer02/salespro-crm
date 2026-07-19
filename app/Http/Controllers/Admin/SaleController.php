<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleStoreRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\CreateSaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $sales = Sale::query()
            ->with(['customer', 'branch'])
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest('sold_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sales.index', [
            'sales' => $sales,
            'search' => $search,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.sales.create', [
            'branches' => Branch::query()
                ->where('status', Branch::STATUS_ACTIVE)
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::query()
                ->with('branchInventories:id,branch_id,product_id,stock_quantity')
                ->where('status', Product::STATUS_ACTIVE)
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'price', 'stock_quantity']),
            'selectedCustomerId' => $request->query('customer_id'),
        ]);
    }

    public function store(SaleStoreRequest $request, CreateSaleService $service): RedirectResponse
    {
        $sale = $service->create($request->validated());

        return redirect()
            ->route('admin.sales.show', $sale)
            ->with('success', 'Sale recorded successfully.');
    }

    public function show(Sale $sale): View
    {
        return view('admin.sales.show', [
            'sale' => $sale->load(['customer', 'branch', 'items.product', 'invoiceEmailLogs']),
        ]);
    }
}
