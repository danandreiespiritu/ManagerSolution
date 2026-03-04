<?php

namespace App\Services\PostingRules;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;

class ARPostingRule implements PostingRuleInterface
{
    public function name(): string
    {
        return 'AR';
    }

    public function validate(array $payload): array
    {
        $lines = $payload['lines'] ?? [];
        $hasReceivable = false;

        foreach ($lines as $i => $ln) {
            // require at least one receivable (debit) line
            $debit = (float) Arr::get($ln, 'debit_amount', 0);
            if ($debit > 0) {
                $hasReceivable = true;
            }
        }

        if (! $hasReceivable) {
            throw ValidationException::withMessages(['lines' => 'AR postings require at least one receivable line.']);
        }

        return $payload;
    }
}
