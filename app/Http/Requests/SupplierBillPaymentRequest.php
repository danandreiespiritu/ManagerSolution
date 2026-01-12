<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierBillPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'supplier_payment_id' => [
                'required',
                'integer',
                Rule::exists('supplier_payments', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'supplier_bill_id' => [
                'required',
                'integer',
                Rule::exists('supplier_bills', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
