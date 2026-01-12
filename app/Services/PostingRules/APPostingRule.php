<?php

namespace App\Services\PostingRules;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

class APPostingRule implements PostingRuleInterface
{
    public function name(): string
    {
        return 'AP';
    }

    public function validate(array $payload): array
    {
        $lines = $payload['lines'] ?? [];
        $hasPayable = false;

        foreach ($lines as $i => $ln) {
            // require supplier_id on payable (credit) lines
            $credit = (float) Arr::get($ln, 'credit_amount', 0);
            if ($credit > 0 && ! Arr::get($ln, 'supplier_id')) {
                throw ValidationException::withMessages(["lines.$i.supplier_id" => 'Supplier is required for payable (credit) lines in AP postings.']);
            }
            if ($credit > 0 && Arr::get($ln, 'supplier_id')) {
                $hasPayable = true;
            }
        }

        if (! $hasPayable) {
            throw ValidationException::withMessages(['lines' => 'AP postings require at least one payable line with supplier linking.']);
        }

        return $payload;
    }
}
