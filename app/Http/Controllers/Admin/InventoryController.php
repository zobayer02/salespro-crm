<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchInventory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $branchId = $request->query('branch_id');
        $search = trim((string) $request->query('search', ''));

        return view('admin.inventory.index', [
            'branches' => Branch::query()->where('status', Branch::STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
            'branchId' => $branchId,
            'search' => $search,
            'inventories' => BranchInventory::query()
                ->with(['branch', 'product'])
                ->when($branchId, function ($query) use ($branchId): void {
                    $query->where('branch_id', $branchId);
                })
                ->when($search !== '', function ($query) use ($search): void {
                    $query->whereHas('product', function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                })
                ->join('products', 'products.id', '=', 'branch_inventories.product_id')
                ->join('branches', 'branches.id', '=', 'branch_inventories.branch_id')
                ->orderBy('branches.name')
                ->orderBy('products.name')
                ->select('branch_inventories.*')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }
}
