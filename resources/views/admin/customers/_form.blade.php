@csrf

<div class="form-grid">
    <div class="field-group">
        <label for="name">Customer Name</label>
        <input id="name" class="field-control" type="text" name="name" value="{{ old('name', $customer->name) }}" required>
        @error('name')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="email">Email</label>
        <input id="email" class="field-control" type="email" name="email" value="{{ old('email', $customer->email) }}" required>
        @error('email')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="phone">Phone</label>
        <input id="phone" class="field-control" type="text" name="phone" value="{{ old('phone', $customer->phone) }}">
        @error('phone')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group" style="grid-column:1 / -1">
        <label for="address">Address</label>
        <textarea id="address" class="field-control" name="address">{{ old('address', $customer->address) }}</textarea>
        @error('address')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="secondary-button" href="{{ route('admin.customers.index') }}">Cancel</a>
    <button class="primary-button" type="submit">{{ $submitLabel }}</button>
</div>
