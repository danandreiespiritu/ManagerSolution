<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfitAndLossReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'accounting_method' => ['required', 'in:accrual,cash'],
            'rounding' => ['nullable', 'in:off,nearest,1,10,100'],
            'footer' => ['nullable', 'string', 'max:500'],
        ];
    }
}
