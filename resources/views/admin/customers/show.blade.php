@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Customer Details'])

@php
    $lastPurchaseAt = $customer->lastPurchaseAt();
    $isLostCustomer = $customer->isLostCustomer($lostCustomerDays);
@endphp

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>{{ $customer->name }}</h2>
                <p>{{ $customer->email }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}</p>
            </div>
            <div class="form-actions" style="margin-top:0">
                <a class="secondary-button" href="{{ route('admin.customers.index') }}">Back</a>
                <a class="primary-button" href="{{ route('admin.customers.edit', $customer) }}">Edit Customer</a>
            </div>
        </div>

        <div class="sale-meta">
            <div class="summary-box">
                <span>Purchase Frequency</span>
                <strong>{{ number_format($customer->sales_count ?? 0) }}</strong>
            </div>
            <div class="summary-box">
                <span>Last Purchase</span>
                <strong>{{ $lastPurchaseAt ? $lastPurchaseAt->format('M d, Y') : 'Never' }}</strong>
            </div>
            <div class="summary-box">
                <span>Total Spent</span>
                <strong>Tk {{ number_format((float) ($customer->total_spent ?? 0), 2) }}</strong>
            </div>
        </div>

        <section class="panel" style="margin-bottom:18px">
            <div class="sale-meta" style="margin-bottom:0">
                <div class="summary-box">
                    <span>CRM Status</span>
                    <strong>{{ $isLostCustomer ? 'Inactive' : 'Active' }}</strong>
                </div>
                <div class="summary-box">
                    <span>Lost Customer Rule</span>
                    <strong>{{ $lostCustomerDays }} days</strong>
                </div>
                <div class="summary-box">
                    <span>Address</span>
                    <strong>{{ $customer->address ?: 'N/A' }}</strong>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <h2>Purchase History</h2>
                <a class="primary-button" href="{{ route('admin.sales.create', ['customer_id' => $customer->id]) }}">Create Sale</a>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Order ID</th>
                            <th>Total Amount</th>
                            <th>Items</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>
                                <td>{{ $sales->firstItem() + $loop->index }}</td>
                                <td>#{{ $sale->order_number }}</td>
                                <td>Tk {{ number_format((float) $sale->total_amount, 2) }}</td>
                                <td>{{ number_format($sale->items_count) }}</td>
                                <td>{{ $sale->sold_at->format('M d, Y') }}</td>
                                <td><span class="status">{{ ucfirst($sale->status) }}</span></td>
                                <td class="text-center"><a class="link-action" href="{{ route('admin.sales.show', $sale) }}">View Sale</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">No purchase history found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($sales->hasPages())
                <div class="pagination">{{ $sales->links() }}</div>
            @endif
        </section>
    </section>
@endsection
