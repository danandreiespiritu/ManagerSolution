<?php

namespace App\Services\PostingRules;

class GeneralPostingRule implements PostingRuleInterface
{
    public function name(): string
    {
        return 'GENERAL';
    }

    public function validate(array $payload): array
    {
        // No additional checks for general postings yet.
        return $payload;
    }
}
