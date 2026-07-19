@csrf

<div class="form-grid">
    <div class="field-group">
        <label for="name">Branch Name</label>
        <input id="name" class="field-control" type="text" name="name" value="{{ old('name', $branch->name) }}" required>
        @error('name')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="code">Branch Code</label>
        <input id="code" class="field-control" type="text" name="code" value="{{ old('code', $branch->code) }}" required>
        @error('code')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="address">Address</label>
        <input id="address" class="field-control" type="text" name="address" value="{{ old('address', $branch->address) }}">
        @error('address')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" class="field-control" name="status" required>
            <option value="active" @selected(old('status', $branch->status) === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $branch->status) === 'inactive')>Inactive</option>
        </select>
        @error('status')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="secondary-button" href="{{ route('admin.branches.index') }}">Cancel</a>
    <button class="primary-button" type="submit">{{ $submitLabel }}</button>
</div>
