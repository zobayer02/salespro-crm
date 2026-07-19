@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'KPI Overview'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>KPI Overview</h2>
                <p>Employee KPI score increases automatically when an assigned customer makes a purchase.</p>
            </div>
        </div>

        <section class="panel">
            <div class="toolbar">
                <div class="filters">
                    <input class="field-control" style="width:320px" type="search" placeholder="Search employee or designation" autocomplete="off" data-live-search>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Employee</th>
                            <th>Designation</th>
                            <th>Total Assigned</th>
                            <th>Active Assignments</th>
                            <th>Converted Customers</th>
                            <th>KPI Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr
                                data-kpi-row
                                data-employee="{{ strtolower($employee->name) }}"
                                data-designation="{{ strtolower($employee->designation) }}"
                                data-status="{{ strtolower($employee->status) }}"
                            >
                                <td>{{ $employees->firstItem() + $loop->index }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->designation }}</td>
                                <td>{{ number_format($employee->assigned_customers_count) }}</td>
                                <td>{{ number_format($employee->active_assignments_count) }}</td>
                                <td>{{ number_format($employee->converted_customers_count) }}</td>
                                <td>{{ number_format($employee->kpi_score) }}</td>
                                <td><span @class(['status', 'inactive' => $employee->status === 'inactive'])>{{ ucfirst($employee->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">No KPI data found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching KPI data found.</div>
            </div>

            @if ($employees->hasPages())
                <div class="pagination">{{ $employees->links() }}</div>
            @endif
        </section>
    </section>

    <script>
        const kpiSearchInput = document.querySelector('[data-live-search]');
        const kpiRows = [...document.querySelectorAll('[data-kpi-row]')];
        const kpiEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleKpiRows = () => {
            const search = kpiSearchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            kpiRows.forEach((row) => {
                const shouldShow = !search
                    || row.dataset.employee.includes(search)
                    || row.dataset.designation.includes(search)
                    || row.dataset.status.includes(search);

                row.hidden = !shouldShow;
                visibleCount += shouldShow ? 1 : 0;
            });

            kpiEmptyState.hidden = visibleCount !== 0;
        };

        kpiSearchInput.addEventListener('input', filterVisibleKpiRows);
        filterVisibleKpiRows();
    </script>
@endsection
