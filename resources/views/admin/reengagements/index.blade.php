@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Re-engagements'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Re-engagement Logs</h2>
                <p>Track email attempts sent to inactive customers.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.reengagements.index') }}">
                <div class="filters">
                    <select class="field-control" style="width:180px" name="status" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="sent" @selected($status === 'sent')>Sent</option>
                        <option value="failed" @selected($status === 'failed')>Failed</option>
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
                            <th>Channel</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $loop->index }}</td>
                                <td><a class="link-action" href="{{ route('admin.customers.show', $log->customer) }}">{{ $log->customer->name }}</a></td>
                                <td>{{ $log->assignment?->employee?->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($log->channel) }}</td>
                                <td><span @class(['status', 'inactive' => $log->status === 'failed'])>{{ ucfirst($log->status) }}</span></td>
                                <td>{{ $log->sent_at ? $log->sent_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td>{{ str($log->message)->limit(70) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">No re-engagement logs found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="pagination">{{ $logs->links() }}</div>
            @endif
        </section>
    </section>
@endsection
