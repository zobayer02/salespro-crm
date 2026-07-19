<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Branch;
use App\Models\Product;
use App\Services\CreateSaleService;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(CreateSaleService::class);
        $branches = Branch::query()->where('status', Branch::STATUS_ACTIVE)->get();
        $customers = Customer::query()->take(5)->get();
        $products = Product::query()->where('status', Product::STATUS_ACTIVE)->get()->keyBy('sku');

        $sales = [
            ['IP15PRO', 1],
            ['SG24', 1],
            ['DELL15', 1],
            ['SONY1000XM5', 2],
            ['AW9', 1],
        ];

        foreach ($sales as $index => [$sku, $quantity]) {
            $product = $products->get($sku);
            $customer = $customers->get($index);
            $branch = $branches->get($index % max($branches->count(), 1));

            if ($product && $customer && $branch) {
                $service->create([
                    'customer_id' => $customer->id,
                    'branch_id' => $branch->id,
                    'send_invoice' => false,
                    'items' => [
                        ['product_id' => $product->id, 'quantity' => $quantity],
                    ],
                ]);
            }
        }
    }
}
