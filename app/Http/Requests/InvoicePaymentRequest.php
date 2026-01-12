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
            'payment_id' => [
                'required',
                'integer',
                Rule::exists('payments', 'id')->where(fn ($q) => $q->where('user_id', $userId)),
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
