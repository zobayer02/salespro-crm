@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Invoices'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Email Invoices</h2>
                <p>Track invoice email delivery attempts after successful sales.</p>
            </div>
        </div>

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.invoices.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:300px" type="search" name="search" value="{{ $search }}" placeholder="Search order, customer or email" autocomplete="off" data-live-search>
                    <select class="field-control" style="width:170px" name="status" data-live-status>
                        <option value="">All Status</option>
                        <option value="sent" @selected($status === 'sent')>Sent</option>
                        <option value="failed" @selected($status === 'failed')>Failed</option>
                    </select>
                </div>
            </form>

            <div class="table-scroll invoice-table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Recipient</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr
                                data-row
                                data-order="{{ strtolower($log->sale->order_number) }}"
                                data-customer="{{ strtolower($log->sale->customer->name) }}"
                                data-branch="{{ strtolower($log->sale->branch?->name ?? 'N/A') }}"
                                data-recipient="{{ strtolower($log->recipient_email) }}"
                                data-status="{{ $log->status }}"
                            >
                                <td>{{ $logs->firstItem() + $loop->index }}</td>
                                <td>#{{ $log->sale->order_number }}</td>
                                <td>{{ $log->sale->customer->name }}</td>
                                <td>{{ $log->sale->branch?->name ?? 'N/A' }}</td>
                                <td>{{ $log->recipient_email }}</td>
                                <td><span @class(['status', 'inactive' => $log->status === 'failed'])>{{ ucfirst($log->status) }}</span></td>
                                <td>{{ $log->sent_at ? $log->sent_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td class="text-center"><a class="link-action" href="{{ route('admin.sales.show', $log->sale) }}">View Sale</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">No invoice email logs found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching invoice logs found.</div>
            </div>

            @if ($logs->hasPages())
                <div class="pagination">{{ $logs->links() }}</div>
            @endif
        </section>
    </section>

    <script>
        const liveFilterForm = document.querySelector('[data-live-filter-form]');
        const liveSearchInput = document.querySelector('[data-live-search]');
        const liveStatusSelect = document.querySelector('[data-live-status]');
        const rows = [...document.querySelectorAll('[data-row]')];
        const liveEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleRows = () => {
            const search = liveSearchInput.value.trim().toLowerCase();
            const status = liveStatusSelect.value;
            let visibleCount = 0;

            rows.forEach((row) => {
                const matchesSearch = !search
                    || row.dataset.order.includes(search)
                    || row.dataset.customer.includes(search)
                    || row.dataset.branch.includes(search)
                    || row.dataset.recipient.includes(search);
                const matchesStatus = !status || row.dataset.status === status;
                const shouldShow = matchesSearch && matchesStatus;

                row.hidden = !shouldShow;
                visibleCount += shouldShow ? 1 : 0;
            });

            liveEmptyState.hidden = visibleCount !== 0;
        };

        liveFilterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            filterVisibleRows();
        });

        liveSearchInput.addEventListener('input', filterVisibleRows);
        liveStatusSelect.addEventListener('change', filterVisibleRows);
        filterVisibleRows();
    </script>
@endsection
