<?php

namespace Tests\Unit;

use App\Services\PostingRules\APPostingRule;
use App\Services\PostingRules\ARPostingRule;
use App\Services\PostingRules\GeneralPostingRule;
use App\Services\PostingRules\PostingRuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostingRuleRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_registry_registers_and_returns_rules(): void
    {
        $registry = new PostingRuleRegistry();
        $registry->register(new APPostingRule());
        $registry->register(new ARPostingRule());
        $registry->register(new GeneralPostingRule());

        $this->assertInstanceOf(APPostingRule::class, $registry->get('AP'));
        $this->assertInstanceOf(ARPostingRule::class, $registry->get('AR'));
        $this->assertInstanceOf(GeneralPostingRule::class, $registry->get('GENERAL'));
    }
}
