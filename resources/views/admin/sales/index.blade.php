@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Sales'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Sales Management</h2>
                <p>Record sales, deduct stock and keep transaction history.</p>
            </div>
            <a class="primary-button" href="{{ route('admin.sales.create') }}">Create Sale</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.sales.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:290px" type="search" name="search" value="{{ $search }}" placeholder="Search order or customer" autocomplete="off" data-live-search>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Total Amount</th>
                            <th>Items</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr data-sale-row data-order="{{ strtolower($sale->order_number) }}" data-customer="{{ strtolower($sale->customer->name) }}">
                                <td>{{ $sales->firstItem() + $loop->index }}</td>
                                <td>#{{ $sale->order_number }}</td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->branch?->name ?? 'N/A' }}</td>
                                <td>Tk {{ number_format((float) $sale->total_amount, 2) }}</td>
                                <td>{{ number_format($sale->items_count ?? $sale->items()->count()) }}</td>
                                <td>{{ $sale->sold_at->format('M d, Y') }}</td>
                                <td><span class="status">{{ ucfirst($sale->status) }}</span></td>
                                <td class="text-center"><a class="link-action" href="{{ route('admin.sales.show', $sale) }}">View</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">No sales found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching sales found.</div>
            </div>

            @if ($sales->hasPages())
                <div class="pagination">{{ $sales->links() }}</div>
            @endif
        </section>
    </section>

    <script>
        const liveFilterForm = document.querySelector('[data-live-filter-form]');
        const liveSearchInput = document.querySelector('[data-live-search]');
        const saleRows = [...document.querySelectorAll('[data-sale-row]')];
        const liveEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleSales = () => {
            const search = liveSearchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            saleRows.forEach((row) => {
                const shouldShow = !search || row.dataset.order.includes(search) || row.dataset.customer.includes(search);
                row.hidden = !shouldShow;
                visibleCount += shouldShow ? 1 : 0;
            });

            liveEmptyState.hidden = visibleCount !== 0;
        };

        liveFilterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            filterVisibleSales();
        });

        liveSearchInput.addEventListener('input', filterVisibleSales);
        filterVisibleSales();
    </script>
@endsection
