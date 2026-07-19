@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Products'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Product Management</h2>
                <p>Manage product catalog, SKU, price and available stock.</p>
            </div>
            <a class="primary-button" href="{{ route('admin.products.create') }}">Add Product</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <form class="toolbar" method="GET" action="{{ route('admin.products.index') }}" data-live-filter-form>
                <div class="filters">
                    <input class="field-control" style="width:260px" type="search" name="search" value="{{ $search }}" placeholder="Search name or SKU" autocomplete="off" data-live-search>
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
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock Quantity</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr data-product-row data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}" data-status="{{ $product->status }}">
                                <td>{{ $products->firstItem() + $loop->index }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>Tk {{ number_format((float) $product->price, 2) }}</td>
                                <td>{{ number_format($product->stock_quantity) }}</td>
                                <td class="text-center">
                                    <span @class(['status', 'inactive' => $product->status === 'inactive'])>
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a class="link-action" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="danger-button" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">No products found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="empty-state" data-live-empty hidden>No matching products found.</div>
            </div>

            @if ($products->hasPages())
                <div class="pagination">{{ $products->links() }}</div>
            @endif
        </section>
    </section>

    <script>
        const liveFilterForm = document.querySelector('[data-live-filter-form]');
        const liveSearchInput = document.querySelector('[data-live-search]');
        const liveStatusSelect = document.querySelector('[data-live-status]');
        const productRows = [...document.querySelectorAll('[data-product-row]')];
        const liveEmptyState = document.querySelector('[data-live-empty]');

        const filterVisibleProducts = () => {
            const search = liveSearchInput.value.trim().toLowerCase();
            const status = liveStatusSelect.value;
            let visibleCount = 0;

            productRows.forEach((row) => {
                const matchesSearch = !search || row.dataset.name.includes(search) || row.dataset.sku.includes(search);
                const matchesStatus = !status || row.dataset.status === status;
                const shouldShow = matchesSearch && matchesStatus;

                row.hidden = !shouldShow;
                visibleCount += shouldShow ? 1 : 0;
            });

            liveEmptyState.hidden = visibleCount !== 0;
        };

        liveFilterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            filterVisibleProducts();
        });

        liveSearchInput.addEventListener('input', filterVisibleProducts);
        liveStatusSelect.addEventListener('change', filterVisibleProducts);
        filterVisibleProducts();
    </script>
@endsection
