<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('branches', 'name')],
            'code' => ['required', 'string', 'max:20', Rule::unique('branches', 'code')],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in([Branch::STATUS_ACTIVE, Branch::STATUS_INACTIVE])],
        ];
    }
}
