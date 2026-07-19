<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceEmailLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        return view('admin.invoices.index', [
            'logs' => InvoiceEmailLog::query()
                ->with(['sale.customer', 'sale.branch'])
                ->when($search !== '', function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query->where('recipient_email', 'like', "%{$search}%")
                            ->orWhereHas('sale', fn ($saleQuery) => $saleQuery->where('order_number', 'like', "%{$search}%"))
                            ->orWhereHas('sale.customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('sale.branch', fn ($branchQuery) => $branchQuery->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when(in_array($status, [InvoiceEmailLog::STATUS_SENT, InvoiceEmailLog::STATUS_FAILED], true), function ($query) use ($status): void {
                    $query->where('status', $status);
                })
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
            'status' => $status,
        ]);
    }
}
