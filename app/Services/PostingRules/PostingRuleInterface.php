<?php

namespace App\Services\PostingRules;

interface PostingRuleInterface
{
    /**
     * Validate payload according to the rule. Should throw ValidationException on failure.
     * Return the (possibly modified) payload.
     *
     * @param array $payload
     * @return array
     */
    public function validate(array $payload): array;

    /**
     * Rule name identifier (e.g., 'AP','AR','GENERAL')
     * @return string
     */
    public function name(): string;
}
