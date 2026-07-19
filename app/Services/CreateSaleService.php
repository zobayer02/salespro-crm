<?php

namespace App\Services;

use App\Mail\SaleInvoiceMail;
use App\Models\BranchInventory;
use App\Models\CustomerAssignment;
use App\Models\InvoiceEmailLog;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateSaleService
{
    public function create(array $data): Sale
    {
        $sale = DB::transaction(function () use ($data): Sale {
            $items = $this->normalizeItems($data['items']);
            $branchId = (int) $data['branch_id'];
            $products = Product::query()
                ->whereIn('id', array_keys($items))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $branchInventories = BranchInventory::query()
                ->where('branch_id', $branchId)
                ->whereIn('product_id', array_keys($items))
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            if ($products->count() !== count($items)) {
                throw ValidationException::withMessages([
                    'items' => 'One or more selected products are invalid.',
                ]);
            }

            if ($branchInventories->count() !== count($items)) {
                throw ValidationException::withMessages([
                    'items' => 'One or more selected products are not available in this branch.',
                ]);
            }

            $totalAmount = 0.0;
            $saleItems = [];

            foreach ($items as $productId => $quantity) {
                $product = $products->get($productId);
                $branchInventory = $branchInventories->get($productId);

                if ($product->status !== Product::STATUS_ACTIVE) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} is not active.",
                    ]);
                }

                if ($branchInventory->stock_quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} has only {$branchInventory->stock_quantity} units available in the selected branch.",
                    ]);
                }

                $unitPrice = (float) $product->price;
                $subtotal = $unitPrice * $quantity;
                $totalAmount += $subtotal;

                $saleItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ];

                $branchInventory->decrement('stock_quantity', $quantity);
                $product->decrement('stock_quantity', $quantity);
            }

            $sale = Sale::query()->create([
                'order_number' => $this->nextOrderNumber(),
                'customer_id' => $data['customer_id'],
                'branch_id' => $branchId,
                'total_amount' => $totalAmount,
                'status' => Sale::STATUS_COMPLETED,
                'sold_at' => now(),
            ]);

            $sale->items()->createMany($saleItems);
            $this->convertActiveAssignment((int) $data['customer_id']);

            return $sale->load(['customer', 'branch', 'items']);
        });

        if (($data['send_invoice'] ?? true) === true) {
            $this->sendInvoiceEmail($sale);
        }

        return $sale;
    }

    private function normalizeItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $quantity = (int) $item['quantity'];

            if ($productId > 0 && $quantity > 0) {
                $normalized[$productId] = ($normalized[$productId] ?? 0) + $quantity;
            }
        }

        if ($normalized === []) {
            throw ValidationException::withMessages([
                'items' => 'Select at least one valid product.',
            ]);
        }

        return $normalized;
    }

    private function nextOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
        } while (Sale::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function convertActiveAssignment(int $customerId): void
    {
        $assignment = CustomerAssignment::query()
            ->where('customer_id', $customerId)
            ->where('status', CustomerAssignment::STATUS_ASSIGNED)
            ->lockForUpdate()
            ->latest('assigned_at')
            ->first();

        if (! $assignment) {
            return;
        }

        $assignment->update([
            'status' => CustomerAssignment::STATUS_CONVERTED,
            'converted_at' => now(),
        ]);

        $assignment->employee()->increment('kpi_score');
    }

    private function sendInvoiceEmail(Sale $sale): void
    {
        try {
            Mail::to($sale->customer->email)->send(new SaleInvoiceMail($sale));

            InvoiceEmailLog::query()->create([
                'sale_id' => $sale->id,
                'recipient_email' => $sale->customer->email,
                'status' => InvoiceEmailLog::STATUS_SENT,
                'sent_at' => now(),
            ]);
        } catch (Throwable $exception) {
            InvoiceEmailLog::query()->create([
                'sale_id' => $sale->id,
                'recipient_email' => $sale->customer->email,
                'status' => InvoiceEmailLog::STATUS_FAILED,
                'failure_reason' => $exception->getMessage(),
            ]);
        }
    }
}
