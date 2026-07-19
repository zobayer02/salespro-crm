@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Assign Customers'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Assign Customers</h2>
                <p>Assign inactive customers to employees for follow-up and conversion tracking.</p>
            </div>
            <a class="primary-button" href="{{ route('admin.assignments.create') }}">New Assignment</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.assignments.index') }}">
                <div class="filters">
                    <select class="field-control" style="width:190px" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="assigned" @selected($status === 'assigned')>Assigned</option>
                        <option value="converted" @selected($status === 'converted')>Converted</option>
                    </select>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Customer</th>
                            <th>Employee</th>
                            <th>Assigned Date</th>
                            <th>Converted Date</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assignments as $assignment)
                            <tr>
                                <td>{{ $assignments->firstItem() + $loop->index }}</td>
                                <td><a class="link-action" href="{{ route('admin.customers.show', $assignment->customer) }}">{{ $assignment->customer->name }}</a></td>
                                <td>{{ $assignment->employee->name }}</td>
                                <td>{{ $assignment->assigned_at->format('M d, Y') }}</td>
                                <td>{{ $assignment->converted_at ? $assignment->converted_at->format('M d, Y') : 'N/A' }}</td>
                                <td><span @class(['status', 'pending' => $assignment->status === 'assigned'])>{{ ucfirst($assignment->status) }}</span></td>
                                <td class="text-center">
                                    @if ($assignment->status === 'assigned')
                                        <button
                                            class="secondary-button"
                                            type="button"
                                            data-compose-email
                                            data-customer-id="{{ $assignment->customer_id }}"
                                            data-assignment-id="{{ $assignment->id }}"
                                            data-customer-name="{{ $assignment->customer->name }}"
                                            data-customer-email="{{ $assignment->customer->email }}"
                                        >Email</button>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">No assignments found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($assignments->hasPages())
                <div class="pagination">{{ $assignments->links() }}</div>
            @endif
        </section>
    </section>

    @include('admin.reengagements._compose_modal')
@endsection
