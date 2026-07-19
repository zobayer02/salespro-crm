@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Inventory'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Branch Inventory</h2>
                <p>Monitor product stock by store location.</p>
            </div>
        </div>

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.inventory.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:280px" type="search" name="search" value="{{ $search }}" placeholder="Search product or SKU" autocomplete="off" data-live-search>
                    <select class="field-control" style="width:240px" name="branch_id" data-live-branch>
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) $branchId === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Branch</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Branch Stock</th>
                            <th>Product Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventories as $inventory)
                            <tr
                                data-inventory-row
                                data-branch-id="{{ $inventory->branch_id }}"
                                data-product="{{ strtolower($inventory->product->name) }}"
                                data-sku="{{ strtolower($inventory->product->sku) }}"
                            >
                                <td>{{ $inventories->firstItem() + $loop->index }}</td>
                                <td>{{ $inventory->branch->name }}</td>
                                <td>{{ $inventory->product->name }}</td>
                                <td>{{ $inventory->product->sku }}</td>
                                <td>{{ number_format($inventory->stock_quantity) }}</td>
                                <td><span @class(['status', 'inactive' => $inventory->product->status === 'inactive'])>{{ ucfirst($inventory->product->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">No inventory records found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching inventory records found.</div>
            </div>

            @if ($inventories->hasPages())
                <div class="pagination">{{ $inventories->links() }}</div>
            @endif
        </section>
    </section>

    <script>
        const liveFilterForm = document.querySelector('[data-live-filter-form]');
        const liveSearchInput = document.querySelector('[data-live-search]');
        const liveBranchSelect = document.querySelector('[data-live-branch]');
        const inventoryRows = [...document.querySelectorAll('[data-inventory-row]')];
        const liveEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleInventory = () => {
            const search = liveSearchInput.value.trim().toLowerCase();
            const branchId = liveBranchSelect.value;
            let visibleCount = 0;

            inventoryRows.forEach((row) => {
                const matchesSearch = !search || row.dataset.product.includes(search) || row.dataset.sku.includes(search);
                const matchesBranch = !branchId || row.dataset.branchId === branchId;
                const shouldShow = matchesSearch && matchesBranch;

                row.hidden = !shouldShow;
                visibleCount += shouldShow ? 1 : 0;
            });

            liveEmptyState.hidden = visibleCount !== 0;
        };

        liveFilterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            filterVisibleInventory();
        });

        liveSearchInput.addEventListener('input', filterVisibleInventory);
        liveBranchSelect.addEventListener('change', () => liveFilterForm.submit());
        filterVisibleInventory();
    </script>
@endsection
