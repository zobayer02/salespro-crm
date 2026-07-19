@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Sale Details'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>#{{ $sale->order_number }}</h2>
                <p>{{ $sale->customer->name }} - {{ $sale->sold_at->format('M d, Y h:i A') }}</p>
            </div>
            <a class="secondary-button" href="{{ route('admin.sales.index') }}">Back to Sales</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="sale-meta">
            <div class="summary-box">
                <span>Customer</span>
                <strong>{{ $sale->customer->name }}</strong>
            </div>
            <div class="summary-box">
                <span>Status</span>
                <strong>{{ ucfirst($sale->status) }}</strong>
            </div>
            <div class="summary-box">
                <span>Branch</span>
                <strong>{{ $sale->branch?->name ?? 'N/A' }}</strong>
            </div>
        </div>

        <div class="sale-meta">
            <div class="summary-box">
                <span>Total Amount</span>
                <strong>Tk {{ number_format((float) $sale->total_amount, 2) }}</strong>
            </div>
            <div class="summary-box">
                <span>Invoice Email</span>
                @php($invoiceLog = $sale->invoiceEmailLogs->sortByDesc('created_at')->first())
                <strong>{{ $invoiceLog ? ucfirst($invoiceLog->status) : 'Not Sent' }}</strong>
            </div>
            <div class="summary-box">
                <span>Invoice Recipient</span>
                <strong>{{ $invoiceLog?->recipient_email ?? $sale->customer->email }}</strong>
            </div>
        </div>

        <section class="panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ number_format($item->quantity) }}</td>
                                <td>Tk {{ number_format((float) $item->unit_price, 2) }}</td>
                                <td>Tk {{ number_format((float) $item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </section>
@endsection
