@csrf

<div class="form-grid">
    <div class="field-group">
        <label for="name">Employee Name</label>
        <input id="name" class="field-control" type="text" name="name" value="{{ old('name', $employee->name) }}" required>
        @error('name')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="email">Email</label>
        <input id="email" class="field-control" type="email" name="email" value="{{ old('email', $employee->email) }}" required>
        @error('email')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="phone">Phone</label>
        <input id="phone" class="field-control" type="text" name="phone" value="{{ old('phone', $employee->phone) }}">
        @error('phone')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="designation">Designation</label>
        <input id="designation" class="field-control" type="text" name="designation" value="{{ old('designation', $employee->designation) }}" required>
        @error('designation')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>

    <div class="field-group">
        <label for="status">Status</label>
        <select id="status" class="field-control" name="status" required>
            <option value="active" @selected(old('status', $employee->status) === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $employee->status) === 'inactive')>Inactive</option>
        </select>
        @error('status')
            <span class="error-text">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a class="secondary-button" href="{{ route('admin.employees.index') }}">Cancel</a>
    <button class="primary-button" type="submit">{{ $submitLabel }}</button>
</div>
