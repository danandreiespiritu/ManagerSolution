<?php

namespace Tests\Unit;

use App\Services\PostingRules\APPostingRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class APPostingRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ap_posting_rule_requires_supplier_on_credit_lines(): void
    {
        $rule = new APPostingRule();

        $this->expectException(ValidationException::class);

        $rule->validate([
            'lines' => [
                ['account_id' => 1, 'credit_amount' => 100],
                ['account_id' => 2, 'debit_amount' => 100],
            ],
        ]);
    }

    public function test_ap_posting_rule_passes_when_supplier_present(): void
    {
        $rule = new APPostingRule();

        $payload = ['lines' => [
            ['account_id' => 1, 'credit_amount' => 100, 'supplier_id' => 5],
            ['account_id' => 2, 'debit_amount' => 100],
        ]];

        $this->assertSame($payload, $rule->validate($payload));
    }
}
