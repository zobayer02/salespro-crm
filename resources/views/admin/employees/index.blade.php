@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Employees'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Employee Management</h2>
                <p>Manage employee records and KPI score baseline for CRM follow-up.</p>
            </div>
            <a class="primary-button" href="{{ route('admin.employees.create') }}">Add Employee</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.employees.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:300px" type="search" name="search" value="{{ $search }}" placeholder="Search name, email, phone or designation" autocomplete="off" data-live-search>
                    <select class="field-control" style="width:170px" name="status" data-live-status>
                        <option value="">All Status</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                    </select>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Employee Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>KPI Score</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr data-row data-name="{{ strtolower($employee->name) }}" data-email="{{ strtolower($employee->email) }}" data-phone="{{ strtolower((string) $employee->phone) }}" data-designation="{{ strtolower($employee->designation) }}" data-status="{{ $employee->status }}">
                                <td>{{ $employees->firstItem() + $loop->index }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone ?: 'N/A' }}</td>
                                <td>{{ $employee->designation }}</td>
                                <td>{{ number_format($employee->kpi_score) }}</td>
                                <td><span @class(['status', 'inactive' => $employee->status === 'inactive'])>{{ ucfirst($employee->status) }}</span></td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <a class="link-action" href="{{ route('admin.employees.edit', $employee) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Delete this employee?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="danger-button" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"><div class="empty-state">No employees found.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching employees found.</div>
            </div>

            @if ($employees->hasPages())
                <div class="pagination">{{ $employees->links() }}</div>
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
                const matchesSearch = !search || row.dataset.name.includes(search) || row.dataset.email.includes(search) || row.dataset.phone.includes(search) || row.dataset.designation.includes(search);
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
