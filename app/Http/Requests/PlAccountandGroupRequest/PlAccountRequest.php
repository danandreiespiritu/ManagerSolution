<?php

namespace App\Http\Requests\PlAccountandGroupRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ChartofAccounts;

class PlAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'account_type' => 'required|string|in:PL',
            'account_name' => 'required|string|max:50',
            'account_code' => 'nullable|string|max:59',
            'account_group' => 'required|string|max:50',
            'cash_flow_category' => 'nullable|string|max:50',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'account_name' => $this->normalizeName($this->input('account_name')),
            'account_code' => $this->normalizeCode($this->input('account_code')),
            'account_group' => $this->normalizeName($this->input('account_group')),
            'cash_flow_category' => $this->normalizeText($this->input('cash_flow_category')),
        ]);
    }

    private function normalizeName($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim((string) $value));
        if ($value === '') {
            return null;
        }

        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    private function normalizeCode($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim((string) $value));
        if ($value === '') {
            return null;
        }

        return mb_strtoupper($value, 'UTF-8');
    }

    private function normalizeText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim((string) $value));
        return $value === '' ? null : $value;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $businessId = null;
            if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
                $businessId = $b->id;
            } elseif (session('current_business_id')) {
                $businessId = session('current_business_id');
            }

            // Account name must contain both upper and lower case characters (not all upper or all lower)
            $name = $this->input('account_name');
            if ($name) {
                if (!preg_match('/[A-Z]/', $name) || !preg_match('/[a-z]/', $name)) {
                    $v->errors()->add('account_name', 'Account name must use proper capitalization (contain both upper and lower case letters).');
                }

                // Case-sensitive duplicate check using BINARY
                $q = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
                    ->whereRaw('BINARY account_name = ?', [$name])
                    ->where('user_id', $this->user()->id)
                    ->where('account_type', 'PL');
                if ($businessId) $q->where('business_id', $businessId);
                if ($this->route('id')) {
                    $q->where('id', '<>', (int)$this->route('id'));
                }
                if ($q->exists()) {
                    $v->errors()->add('account_name', 'An account with this exact name already exists.');
                }
            }

            $code = $this->input('account_code');
            if ($code) {
                $q = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
                    ->whereRaw('BINARY account_code = ?', [$code])
                    ->where('user_id', $this->user()->id)
                    ->where('account_type', 'PL');
                if ($businessId) $q->where('business_id', $businessId);
                if ($this->route('id')) {
                    $q->where('id', '<>', (int)$this->route('id'));
                }
                if ($q->exists()) {
                    $v->errors()->add('account_code', 'An account with this exact code already exists.');
                }
            }
        });
    }
}
