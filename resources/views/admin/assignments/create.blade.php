@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'New Assignment'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>New Assignment</h2>
                <p>Only customers inactive for {{ $lostCustomerDays }} days or never purchased are available.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" action="{{ route('admin.assignments.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="field-group">
                        <label for="customer_id">Inactive Customer</label>
                        <select id="customer_id" class="field-control" name="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                @php
                                    $lastPurchaseAt = $customer->lastPurchaseAt();
                                @endphp
                                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $selectedCustomerId ?? '') === (string) $customer->id)>
                                    {{ $customer->name }} - Last Purchase: {{ $lastPurchaseAt ? $lastPurchaseAt->format('M d, Y') : 'Never' }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-group">
                        <label for="employee_id">Employee</label>
                        <select id="employee_id" class="field-control" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected((string) old('employee_id') === (string) $employee->id)>
                                    {{ $employee->name }} - {{ $employee->designation }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a class="secondary-button" href="{{ route('admin.assignments.index') }}">Cancel</a>
                    <button class="primary-button" type="submit">Assign Customer</button>
                </div>
            </form>
        </section>
    </section>
@endsection
