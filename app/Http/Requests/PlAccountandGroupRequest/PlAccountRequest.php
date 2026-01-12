<?php

namespace App\Http\Requests\PlAccountandGroupRequest;

use Illuminate\Foundation\Http\FormRequest;

class PlAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_type' => 'required|string|in:PL',
            'account_name' => 'required|string|max:50',
            'account_code' => 'nullable|string|max:59',
            'account_group' => 'required|string|max:50',
            'cash_flow_category' => 'nullable|string|max:50',
        ];
    }
}
