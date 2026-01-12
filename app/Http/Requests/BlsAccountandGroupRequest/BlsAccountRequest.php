<?php

namespace App\Http\Requests\BlsAccountandGroupRequest;

use Illuminate\Foundation\Http\FormRequest;

class BlsAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_type' => 'required|string|in:BL',
            'account_name' => 'required|string|max:50',
            'account_code' => 'nullable|string|max:59',
            'account_group' => 'required|string|max:50',
            'cash_flow_category' => 'nullable|string|max:50',
        ];
    }
}
