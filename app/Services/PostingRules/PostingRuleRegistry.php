<?php

namespace App\Services\PostingRules;

use Illuminate\Support\Collection;

class PostingRuleRegistry
{
    protected array $rules = [];

    public function register(PostingRuleInterface $rule): void
    {
        $this->rules[$rule->name()] = $rule;
    }

    public function get(string $name): ?PostingRuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    public function all(): array
    {
        return $this->rules;
    }
}
