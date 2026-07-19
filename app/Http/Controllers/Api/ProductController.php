<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductIntegrationResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'sku' => ['nullable', 'string', 'max:80'],
        ]);

        $products = Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->when(! empty($validated['search']), function ($query) use ($validated): void {
                $search = $validated['search'];

                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when(! empty($validated['sku']), function ($query) use ($validated): void {
                $query->where('sku', $validated['sku']);
            })
            ->orderBy('name')
            ->paginate(20);

        return ProductIntegrationResource::collection($products);
    }

    public function show(string $sku): ProductIntegrationResource
    {
        $product = Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->where('sku', $sku)
            ->firstOrFail();

        return ProductIntegrationResource::make($product);
    }
}
