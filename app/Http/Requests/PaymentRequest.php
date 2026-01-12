<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
            'payment_date' => ['required','date'],
            'amount' => ['required','numeric','min:0.01'],
            'payment_type' => ['required','string','max:50'],
            'customer_id' => ['nullable','integer','exists:customers,id'],
            'supplier_id' => ['nullable','integer','exists:suppliers,id'],
            'cash_account_id' => [
                'nullable',
                'integer',
                Rule::exists('chart_of_accounts', 'id')->where(fn ($q) => $q->where('business_id', $businessId)),
            ],
            'reference' => ['nullable','string','max:255'],
            'allocations' => ['nullable','array'],
            'allocations.*.invoice_id' => ['required_with:allocations','integer','exists:customer_invoices,id'],
            'allocations.*.amount' => ['required_with:allocations','numeric','min:0.01'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $amount = (float) $this->input('amount', 0);
            $allocs = $this->input('allocations', []);

            $sum = 0.0;
            if (is_array($allocs)) {
                foreach ($allocs as $a) {
                    $sum += (float) ($a['amount'] ?? 0);
                }
            }

            if ($sum > $amount + 0.0001) {
                $v->errors()->add('allocations', 'Total allocation amount exceeds payment amount.');
            }
        });
    }
}
