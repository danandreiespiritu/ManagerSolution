<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_code' => ['nullable','string','max:50'],
            'customer_name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'is_active' => ['sometimes','boolean'],
            'business_id' => ['nullable','integer','exists:businesses,id'],
        ];
    }
}
