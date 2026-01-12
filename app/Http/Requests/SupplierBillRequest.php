<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierBillRequest extends FormRequest
{
    private function currentBusinessId(): ?int
    {
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            return $b->id ?? null;
        }

        $id = session('current_business_id');
        return $id ? (int) $id : null;
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $businessId = $this->currentBusinessId() ?? 0;

        return [
            'bill_number' => ['required', 'string', 'max:100'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'bill_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:bill_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'expense_account_id' => [
                'required',
                'integer',
                Rule::exists('chart_of_accounts', 'id')->where(fn ($q) => $q->where('business_id', $businessId)),
            ],
            'ap_account_id' => [
                'required',
                'integer',
                Rule::exists('chart_of_accounts', 'id')->where(fn ($q) => $q->where('business_id', $businessId)),
            ],
        ];
    }
}
