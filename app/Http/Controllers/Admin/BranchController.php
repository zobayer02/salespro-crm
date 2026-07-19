<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BranchStoreRequest;
use App\Http\Requests\BranchUpdateRequest;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        return view('admin.branches.index', [
            'search' => $search,
            'branches' => Branch::query()
                ->withCount('sales')
                ->withSum('inventories as total_stock', 'stock_quantity')
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.branches.create', [
            'branch' => new Branch(['status' => Branch::STATUS_ACTIVE]),
        ]);
    }

    public function store(BranchStoreRequest $request): RedirectResponse
    {
        $branch = Branch::query()->create($request->validated());

        Product::query()->get(['id'])->each(function (Product $product) use ($branch): void {
            BranchInventory::query()->create([
                'branch_id' => $branch->id,
                'product_id' => $product->id,
                'stock_quantity' => 0,
            ]);
        });

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch): View
    {
        return view('admin.branches.edit', [
            'branch' => $branch,
        ]);
    }

    public function update(BranchUpdateRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->validated());

        return redirect()
            ->route('admin.branches.index')
            ->with('success', 'Branch updated successfully.');
    }
}
