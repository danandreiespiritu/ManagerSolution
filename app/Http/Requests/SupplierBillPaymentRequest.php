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
            'allocation_type' => ['required', 'in:payment,credit,debit'],

            'supplier_payment_id' => [
                'nullable',
                'integer',
                Rule::exists('supplier_payments', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
                'required_if:allocation_type,payment',
            ],

            'supplier_credit_note_id' => [
                'nullable',
                'integer',
                Rule::exists('supplier_credit_notes', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
                'required_if:allocation_type,credit',
            ],

            'supplier_debit_note_id' => [
                'nullable',
                'integer',
                Rule::exists('supplier_debit_notes', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
                'required_if:allocation_type,debit',
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
