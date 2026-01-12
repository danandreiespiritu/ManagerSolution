<?php

namespace App\Services;

use App\Models\Business;
use App\Models\ChartofAccounts;
use Illuminate\Support\Facades\DB;

class StandardCoaService
{
    /**
     * Ensure the standard COA exists for the given business.
     * Idempotent: creates missing accounts, updates name/group/type for existing ones.
     */
    public function ensureForBusiness(int $userId, int $businessId): void
    {
        $accounts = config('standard_coa.accounts', []);

        DB::transaction(function () use ($accounts, $userId, $businessId) {
            // IMPORTANT:
            // Models using BelongsToBusiness attach creating/updating hooks that can override
            // business_id from the current session. During seeding we must preserve the
            // explicit $businessId passed in.
            ChartofAccounts::withoutEvents(function () use ($accounts, $userId, $businessId) {
                foreach ($accounts as $acct) {
                    $code = (string) ($acct['code'] ?? '');
                    $name = (string) ($acct['name'] ?? '');
                    if ($code === '' || $name === '') {
                        continue;
                    }

                    $statement = (string) ($acct['statement'] ?? '');
                    // The rest of the application expects account_type in {BL, PL}.
                    $accountType = $statement === 'profit-loss' ? 'PL' : 'BL';

                    ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
                        ->updateOrCreate(
                            [
                                'business_id' => $businessId,
                                'account_code' => $code,
                            ],
                            [
                                'user_id' => $userId,
                                'account_type' => $accountType,
                                'account_name' => $name,
                                'account_group' => $acct['group'] ?? null,
                                'group' => $acct['group'] ?? null,
                                'group_category' => $acct['group_category'] ?? null,
                                'cash_flow_category' => $acct['cash_flow_category'] ?? null,
                                'classification' => $acct['type'] ?? null,
                                'is_active' => true,
                                'is_control_account' => (bool) ($acct['is_control_account'] ?? false),
                            ]
                        );
                }
            });
        });
    }

    /**
     * Seed standard COA for all businesses in the system.
     */
    public function ensureForAllBusinesses(): void
    {
        /** @var \Illuminate\Support\Collection<int, Business> $businesses */
        $businesses = Business::all();

        foreach ($businesses as $biz) {
            if (!$biz->user_id || !$biz->id) {
                continue;
            }
            $this->ensureForBusiness((int) $biz->user_id, (int) $biz->id);
        }
    }

    /**
     * Resolve an account by code within a business.
     */
    public function findByCode(?int $businessId, string $code): ?ChartofAccounts
    {
        if (!$businessId || $code === '') {
            return null;
        }

        return ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
            ->where('business_id', $businessId)
            ->where('account_code', $code)
            ->first();
    }

    /**
     * Resolve an account code for the given text using config('standard_coa.keyword_map').
     * Longest keyword wins. Optionally restrict matches to a set of allowed codes.
     */
    public function resolveCodeForText(?string $text, ?array $allowedCodes = null): ?string
    {
        $haystack = mb_strtolower(trim((string) ($text ?? '')));
        if ($haystack === '') {
            return null;
        }

        /** @var array<string, string> $map */
        $map = config('standard_coa.keyword_map', []);

        $bestCode = null;
        $bestLen = 0;

        foreach ($map as $keyword => $code) {
            $kw = mb_strtolower((string) $keyword);
            if ($kw === '') {
                continue;
            }
            if ($allowedCodes !== null && !in_array((string) $code, $allowedCodes, true)) {
                continue;
            }
            if (mb_strpos($haystack, $kw) !== false) {
                $len = mb_strlen($kw);
                if ($len > $bestLen) {
                    $bestLen = $len;
                    $bestCode = (string) $code;
                }
            }
        }

        return $bestCode;
    }

    /**
     * Resolve an account within a business using keyword mapping, with a fallback code.
     */
    public function resolveAccountForText(?int $businessId, ?string $text, string $fallbackCode, ?array $allowedCodes = null): ?ChartofAccounts
    {
        $code = $this->resolveCodeForText($text, $allowedCodes) ?: $fallbackCode;
        return $this->findByCode($businessId, $code);
    }
}
