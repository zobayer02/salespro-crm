@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'Create Sale'])

@php
    $initialItems = old('items', [['product_id' => '', 'quantity' => 1]]);
    $productStocks = $products->mapWithKeys(fn ($product) => [
        $product->id => $product->branchInventories->mapWithKeys(fn ($inventory) => [
            $inventory->branch_id => $inventory->stock_quantity,
        ])->all(),
    ])->all();
@endphp

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>Create Sale</h2>
                <p>Select customer and products. Stock will deduct after successful sale.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.sales.store') }}" data-sale-form>
                @csrf

                <div class="field-group">
                    <label for="branch_id">Branch</label>
                    <select id="branch_id" class="field-control" name="branch_id" required data-branch-select>
                        <option value="">Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label for="customer_id">Customer</label>
                    <select id="customer_id" class="field-control" name="customer_id" required>
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) old('customer_id', $selectedCustomerId ?? '') === (string) $customer->id)>
                                {{ $customer->name }} - {{ $customer->email }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                @error('items')
                    <div class="alert" style="color:#dc2626;background:#fff5f5;border:1px solid #fecaca">{{ $message }}</div>
                @enderror

                <div class="sale-items" data-sale-items>
                    @foreach ($initialItems as $item)
                        <div class="sale-item-row" data-sale-item-row>
                            <div class="field-group" style="margin-bottom:0">
                                <label>Product</label>
                                <select class="field-control" name="items[{{ $loop->index }}][product_id]" required data-product-select>
                                    <option value="">Select Product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }} ({{ $product->sku }})" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>
                                            {{ $product->name }} ({{ $product->sku }}) - Stock {{ $product->stock_quantity }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field-group" style="margin-bottom:0">
                                <label>Quantity</label>
                                <input class="field-control" type="number" name="items[{{ $loop->index }}][quantity]" min="1" step="1" value="{{ $item['quantity'] ?? 1 }}" required data-quantity-input>
                            </div>

                            <div class="summary-box">
                                <span>Unit Price</span>
                                <strong data-unit-price>Tk 0.00</strong>
                            </div>

                            <div class="summary-box">
                                <span>Subtotal</span>
                                <strong data-subtotal>Tk 0.00</strong>
                            </div>

                            <button class="icon-danger" type="button" data-remove-item>&times;</button>
                        </div>
                    @endforeach
                </div>

                <div class="sale-meta">
                    <div class="summary-box">
                        <span>Total Items</span>
                        <strong data-total-items>0</strong>
                    </div>
                    <div class="summary-box">
                        <span>Total Quantity</span>
                        <strong data-total-quantity>0</strong>
                    </div>
                    <div class="summary-box">
                        <span>Total Amount</span>
                        <strong data-total-amount>Tk 0.00</strong>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="secondary-button" type="button" data-add-item>Add Item</button>
                    <a class="secondary-button" href="{{ route('admin.sales.index') }}">Cancel</a>
                    <button class="primary-button" type="submit">Record Sale</button>
                </div>
            </form>
        </section>
    </section>

    <script>
        const saleItems = document.querySelector('[data-sale-items]');
        const addItemButton = document.querySelector('[data-add-item]');
        const branchSelect = document.querySelector('[data-branch-select]');
        const totalItems = document.querySelector('[data-total-items]');
        const totalQuantity = document.querySelector('[data-total-quantity]');
        const totalAmount = document.querySelector('[data-total-amount]');
        const branchStocks = @json($productStocks);

        const formatCurrency = (amount) => `Tk ${amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        const refreshSaleRows = () => {
            const rows = [...document.querySelectorAll('[data-sale-item-row]')];
            let quantitySum = 0;
            let amountSum = 0;
            const branchId = branchSelect.value;

            rows.forEach((row, index) => {
                const select = row.querySelector('[data-product-select]');
                [...select.options].forEach((option) => {
                    if (!option.value) {
                        return;
                    }

                    const stock = branchStocks[option.value]?.[branchId] ?? 0;
                    option.dataset.stock = stock;
                    option.textContent = `${option.dataset.name} - Branch Stock ${stock}`;
                });

                const quantityInput = row.querySelector('[data-quantity-input]');
                const selectedOption = select.options[select.selectedIndex];
                const price = Number(selectedOption?.dataset.price || 0);
                const quantity = Number(quantityInput.value || 0);
                const subtotal = price * quantity;

                select.name = `items[${index}][product_id]`;
                quantityInput.name = `items[${index}][quantity]`;
                row.querySelector('[data-unit-price]').textContent = formatCurrency(price);
                row.querySelector('[data-subtotal]').textContent = formatCurrency(subtotal);

                quantitySum += quantity;
                amountSum += subtotal;
            });

            totalItems.textContent = rows.length.toString();
            totalQuantity.textContent = quantitySum.toLocaleString('en-US');
            totalAmount.textContent = formatCurrency(amountSum);
        };

        const bindSaleRow = (row) => {
            row.querySelector('[data-product-select]').addEventListener('change', refreshSaleRows);
            row.querySelector('[data-quantity-input]').addEventListener('input', refreshSaleRows);
            row.querySelector('[data-remove-item]').addEventListener('click', () => {
                if (document.querySelectorAll('[data-sale-item-row]').length > 1) {
                    row.remove();
                    refreshSaleRows();
                }
            });
        };

        addItemButton.addEventListener('click', () => {
            const firstRow = document.querySelector('[data-sale-item-row]');
            const clone = firstRow.cloneNode(true);
            clone.querySelectorAll('.custom-filter').forEach((dropdown) => dropdown.remove());
            const productSelect = clone.querySelector('[data-product-select]');
            productSelect.value = '';
            productSelect.classList.remove('filter-select-native');
            productSelect.removeAttribute('data-filter-enhanced');
            clone.querySelector('[data-quantity-input]').value = 1;
            saleItems.appendChild(clone);
            window.enhanceSalesProFilters?.();
            bindSaleRow(clone);
            refreshSaleRows();
        });

        branchSelect.addEventListener('change', refreshSaleRows);
        document.querySelectorAll('[data-sale-item-row]').forEach(bindSaleRow);
        refreshSaleRows();
    </script>
@endsection
