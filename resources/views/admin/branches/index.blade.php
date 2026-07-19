@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Branches'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Branch Management</h2>
                <p>Manage store locations and track branch-wise stock and sales.</p>
            </div>
            <a class="primary-button" href="{{ route('admin.branches.create') }}">Add Branch</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.branches.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:320px" type="search" name="search" value="{{ $search }}" placeholder="Search branch, code or address" autocomplete="off" data-live-search>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Branch Name</th>
                            <th>Code</th>
                            <th>Address</th>
                            <th>Total Stock</th>
                            <th>Sales</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $branch)
                            <tr
                                data-branch-row
                                data-name="{{ strtolower($branch->name) }}"
                                data-code="{{ strtolower($branch->code) }}"
                                data-address="{{ strtolower((string) $branch->address) }}"
                            >
                                <td>{{ $branches->firstItem() + $loop->index }}</td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->code }}</td>
                                <td>{{ $branch->address ?: 'N/A' }}</td>
                                <td>{{ number_format((int) ($branch->total_stock ?? 0)) }}</td>
                                <td>{{ number_format((int) $branch->sales_count) }}</td>
                                <td><span @class(['status', 'inactive' => $branch->status === 'inactive'])>{{ ucfirst($branch->status) }}</span></td>
                                <td class="text-center"><a class="link-action" href="{{ route('admin.branches.edit', $branch) }}">Edit</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">No branches found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching branches found.</div>
            </div>

            @if ($branches->hasPages())
                <div class="pagination">{{ $branches->links() }}</div>
            @endif
        </section>
    </section>

    <script>
        const liveFilterForm = document.querySelector('[data-live-filter-form]');
        const liveSearchInput = document.querySelector('[data-live-search]');
        const branchRows = [...document.querySelectorAll('[data-branch-row]')];
        const liveEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleBranches = () => {
            const search = liveSearchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            branchRows.forEach((row) => {
                const shouldShow = !search
                    || row.dataset.name.includes(search)
                    || row.dataset.code.includes(search)
                    || row.dataset.address.includes(search);

                row.hidden = !shouldShow;
                visibleCount += shouldShow ? 1 : 0;
            });

            liveEmptyState.hidden = visibleCount !== 0;
        };

        liveFilterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            filterVisibleBranches();
        });

        liveSearchInput.addEventListener('input', filterVisibleBranches);
        filterVisibleBranches();
    </script>
@endsection
