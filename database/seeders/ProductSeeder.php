<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['iPhone 15 Pro', 'IP15PRO', 135000, 24],
            ['Samsung Galaxy S24', 'SG24', 118000, 32],
            ['Dell Inspiron 15', 'DELL15', 78000, 18],
            ['Sony WH-1000XM5', 'SONY1000XM5', 36000, 42],
            ['Apple Watch Series 9', 'AW9', 52000, 27],
            ['Logitech MX Master 3S', 'MXM3S', 12500, 60],
            ['Asus Zenbook 14', 'ZEN14', 145000, 12],
            ['Canon EOS R50', 'EOSR50', 92000, 9],
        ];

        foreach ($products as [$name, $sku, $price, $stockQuantity]) {
            Product::query()->updateOrCreate([
                'sku' => $sku,
            ], [
                'name' => $name,
                'price' => $price,
                'stock_quantity' => $stockQuantity,
                'status' => Product::STATUS_ACTIVE,
            ]);
        }
    }
}
