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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->normalizeName($this->input('name')),
            'category' => $this->normalizeName($this->input('category')),
        ]);
    }

    private function normalizeName($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim((string) $value));
        if ($value === '') {
            return null;
        }

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}
