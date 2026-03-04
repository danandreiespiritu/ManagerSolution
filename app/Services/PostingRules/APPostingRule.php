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
            // require at least one payable (credit) line
            $credit = (float) Arr::get($ln, 'credit_amount', 0);
            if ($credit > 0) {
                $hasPayable = true;
            }
        }

        if (! $hasPayable) {
            throw ValidationException::withMessages(['lines' => 'AP postings require at least one payable line.']);
        }

        return $payload;
    }
}
