<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['required','string','max:100'],
            'customer_id' => ['required','integer','exists:customers,id'],
            'invoice_date' => ['required','date'],
            'due_date' => ['nullable','date','after_or_equal:invoice_date'],
            'total_amount' => ['required','numeric'],
            'status' => ['nullable','string','max:50'],
            'business_id' => ['nullable','integer','exists:businesses,id'],
        ];
    }
}
