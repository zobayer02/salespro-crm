<?php

namespace Database\Seeders;

use App\Models\InvoiceEmailLog;
use App\Models\Sale;
use Illuminate\Database\Seeder;

class InvoiceEmailLogSeeder extends Seeder
{
    public function run(): void
    {
        Sale::query()
            ->with('customer')
            ->latest('sold_at')
            ->take(8)
            ->get()
            ->each(function (Sale $sale, int $index): void {
                $status = $index === 2 ? InvoiceEmailLog::STATUS_FAILED : InvoiceEmailLog::STATUS_SENT;

                InvoiceEmailLog::query()->updateOrCreate(
                    ['sale_id' => $sale->id],
                    [
                        'recipient_email' => $sale->customer->email,
                        'status' => $status,
                        'failure_reason' => $status === InvoiceEmailLog::STATUS_FAILED ? 'SMTP delivery timeout during sample run.' : null,
                        'sent_at' => $status === InvoiceEmailLog::STATUS_SENT ? $sale->sold_at->copy()->addMinutes(3) : null,
                    ],
                );
            });
    }
}
