<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BranchInventorySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::query()->orderBy('id')->get();

        if ($branches->isEmpty()) {
            return;
        }

        Product::query()->get()->each(function (Product $product) use ($branches): void {
            $remainingStock = (int) $product->stock_quantity;
            $branchCount = $branches->count();

            foreach ($branches as $index => $branch) {
                $quantity = $index === $branchCount - 1
                    ? $remainingStock
                    : (int) floor((int) $product->stock_quantity / $branchCount);

                BranchInventory::query()->updateOrCreate([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                ], [
                    'stock_quantity' => $quantity,
                ]);

                $remainingStock -= $quantity;
            }
        });
    }
}
