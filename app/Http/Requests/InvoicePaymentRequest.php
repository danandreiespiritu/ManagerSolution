<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoicePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'allocation_type' => ['required', 'in:payment,credit'],

            'payment_id' => [
                'nullable',
                'integer',
                Rule::exists('payments', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
                'required_if:allocation_type,payment',
            ],

            'customer_credit_note_id' => [
                'nullable',
                'integer',
                Rule::exists('customer_credit_notes', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
                'required_if:allocation_type,credit',
            ],

            'customer_invoice_id' => [
                'required',
                'integer',
                Rule::exists('customer_invoices', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
            ],

            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
