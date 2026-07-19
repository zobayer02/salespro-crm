<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, [Product::STATUS_ACTIVE, Product::STATUS_INACTIVE], true), function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product([
                'status' => Product::STATUS_ACTIVE,
            ]),
        ]);
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $product = Product::query()->create($request->validated());
        $firstBranchId = Branch::query()->where('status', Branch::STATUS_ACTIVE)->oldest()->value('id');

        Branch::query()->get(['id'])->each(function (Branch $branch) use ($product, $firstBranchId): void {
            BranchInventory::query()->create([
                'branch_id' => $branch->id,
                'product_id' => $product->id,
                'stock_quantity' => $branch->id === $firstBranchId ? $product->stock_quantity : 0,
            ]);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $previousStock = (int) $product->stock_quantity;
        $product->update($request->validated());
        $stockDelta = (int) $product->stock_quantity - $previousStock;

        if ($stockDelta !== 0) {
            $firstInventory = $product->branchInventories()->oldest()->first();
            $firstInventory?->update([
                'stock_quantity' => max(0, $firstInventory->stock_quantity + $stockDelta),
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
