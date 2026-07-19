@csrf

<div class="form-grid">
    <div class="field-group">
        <label for="name">Product Name</label>
        <input id="name" class="field-control" type="text" name="name" value="{{ old('name', $product->name) }}" required>
        @error('name')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="sku">SKU</label>
        <input id="sku" class="field-control" type="text" name="sku" value="{{ old('sku', $product->sku) }}" required>
        @error('sku')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="price">Price</label>
        <input id="price" class="field-control" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
        @error('price')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="stock_quantity">Stock Quantity</label>
        <input id="stock_quantity" class="field-control" type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0" step="1" required>
        @error('stock_quantity')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" class="field-control" name="status" required>
            <option value="active" @selected(old('status', $product->status) === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Inactive</option>
        </select>
        @error('status')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="secondary-button" href="{{ route('admin.products.index') }}">Cancel</a>
    <button class="primary-button" type="submit">{{ $submitLabel }}</button>
</div>
