<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerAssignmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')->where('status', Employee::STATUS_ACTIVE),
            ],
        ];
    }
}
