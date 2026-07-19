<?php

namespace App\Http\Requests;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        $branch = $this->route('branch');

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('branches', 'name')->ignore($branch)],
            'code' => ['required', 'string', 'max:20', Rule::unique('branches', 'code')->ignore($branch)],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in([Branch::STATUS_ACTIVE, Branch::STATUS_INACTIVE])],
        ];
    }
}
