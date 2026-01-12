<?php

namespace App\Http\Requests\PlAccountandGroupRequest;

use Illuminate\Foundation\Http\FormRequest;

class PlGroupRequest extends FormRequest
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
