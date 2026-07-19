@php
    $isLostView = ($mode ?? 'index') === 'lost';
@endphp

@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => $isLostView ? 'Inactive Customers' : 'Customers'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>{{ $isLostView ? 'Inactive Customers' : 'Customer Management' }}</h2>
                <p>{{ $isLostView ? "Customers with no purchase within {$lostCustomerDays} days." : 'Manage customer contact information and purchase history for CRM workflows.' }}</p>
            </div>
            <a class="primary-button" href="{{ route('admin.customers.create') }}">Add Customer</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ $isLostView ? route('admin.customers.inactive') : route('admin.customers.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:280px" type="search" name="search" value="{{ $search }}" placeholder="Search name, email or phone" autocomplete="off" data-live-search>
                    @unless ($isLostView)
                        <select class="field-control" style="width:190px" name="status" data-live-status>
                            <option value="">All CRM Status</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="lost" @selected($status === 'lost')>Inactive</option>
                        </select>
                    @endunless
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Purchases</th>
                            <th>Last Purchase</th>
                            <th>Total Spent</th>
                            <th>CRM Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            @php
                                $lastPurchaseAt = $customer->lastPurchaseAt();
                                $isLostCustomer = $customer->isLostCustomer($lostCustomerDays);
                                $crmStatus = $isLostCustomer ? 'lost' : 'active';
                            @endphp
                            <tr data-row data-name="{{ strtolower($customer->name) }}" data-email="{{ strtolower($customer->email) }}" data-phone="{{ strtolower((string) $customer->phone) }}" data-status="{{ $crmStatus }}">
                                <td>{{ $customers->firstItem() + $loop->index }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?: 'N/A' }}</td>
                                <td>{{ number_format($customer->sales_count ?? 0) }}</td>
                                <td>{{ $lastPurchaseAt ? $lastPurchaseAt->format('M d, Y') : 'Never' }}</td>
                                <td>Tk {{ number_format((float) ($customer->total_spent ?? 0), 2) }}</td>
                                <td><span @class(['status', 'inactive' => $isLostCustomer])>{{ $isLostCustomer ? 'Inactive' : 'Active' }}</span></td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <a class="link-action" href="{{ route('admin.customers.show', $customer) }}">View</a>
                                        @if ($isLostCustomer && (int) ($customer->active_assignments_count ?? 0) === 0)
                                            <a class="link-action" href="{{ route('admin.assignments.create', ['customer_id' => $customer->id]) }}">Assign</a>
                                        @elseif ($isLostCustomer)
                                            <span class="status pending">Assigned</span>
                                        @endif
                                        @if ($isLostCustomer)
                                            <button
                                                class="secondary-button"
                                                type="button"
                                                data-compose-email
                                                data-customer-id="{{ $customer->id }}"
                                                data-customer-name="{{ $customer->name }}"
                                                data-customer-email="{{ $customer->email }}"
                                            >Email</button>
                                        @endif
                                        <a class="link-action" href="{{ route('admin.customers.edit', $customer) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="danger-button" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9"><div class="empty-state">{{ $isLostView ? 'No inactive customers found.' : 'No customers found.' }}</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching customers found.</div>
            </div>

            @if ($customers->hasPages())
                <div class="pagination">{{ $customers->links() }}</div>
            @endif
        </section>
    </section>

    @include('admin.reengagements._compose_modal')

    <script>
        const liveFilterForm = document.querySelector('[data-live-filter-form]');
        const liveSearchInput = document.querySelector('[data-live-search]');
        const liveStatusSelect = document.querySelector('[data-live-status]');
        const rows = [...document.querySelectorAll('[data-row]')];
        const liveEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleRows = () => {
            const search = liveSearchInput.value.trim().toLowerCase();
            const status = liveStatusSelect?.value || '';
            let visibleCount = 0;

            rows.forEach((row) => {
                const matchesSearch = !search || row.dataset.name.includes(search) || row.dataset.email.includes(search) || row.dataset.phone.includes(search);
                const matchesStatus = !liveStatusSelect || !status || row.dataset.status === status;
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
        liveStatusSelect?.addEventListener('change', filterVisibleRows);
        filterVisibleRows();
    </script>
@endsection
