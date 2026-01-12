<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreditNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'credit_note_number' => ['required', 'string', 'max:100'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'credit_date' => ['required', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
