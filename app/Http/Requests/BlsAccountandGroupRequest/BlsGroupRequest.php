<?php

namespace App\Http\Requests\BlsAccountandGroupRequest;

use Illuminate\Foundation\Http\FormRequest;

class BlsGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
        ];
    }
}
