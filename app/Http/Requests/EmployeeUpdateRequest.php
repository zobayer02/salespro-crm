<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('employees', 'email')->ignore($employee)],
            'phone' => ['nullable', 'string', 'max:40'],
            'designation' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in([Employee::STATUS_ACTIVE, Employee::STATUS_INACTIVE])],
        ];
    }
}
