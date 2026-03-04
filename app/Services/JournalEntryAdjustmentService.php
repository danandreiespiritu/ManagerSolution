<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\JournalEntryAdjustment;
use App\Models\ChartofAccounts;
use App\Models\JournalEntryAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class JournalEntryAdjustmentService
{
    /**
     * Determine if a journal entry has an imbalance
     */
    public function hasImbalance(JournalEntry $entry): bool
    {
        return $entry->hasImbalance();
    }

    /**
     * Get the imbalance amount
     * Positive = more debits, Negative = more credits
     */
    public function getImbalanceAmount(JournalEntry $entry)
    {
        return $entry->getImbalanceAmount();
    }

    /**
     * Check if the entry can be adjusted (has imbalance and no existing adjustments)
     */
    public function canBeAdjusted(JournalEntry $entry): bool
    {
        return $this->hasImbalance($entry) && $entry->adjustments()->count() === 0;
    }

    /**
     * Automatically generate an adjustment entry to balance the original entry
     * Returns the created adjustment entry or null if no adjustment needed
     * 
     * @throws InvalidArgumentException If entry already has adjustments
     */
    public function createAdjustmentEntry(
        JournalEntry $entry,
        ?int $adjustmentAccountId = null,
        ?string $reason = null
    ): ?JournalEntryAdjustment {
        return DB::transaction(function () use ($entry, $adjustmentAccountId, $reason) {
            // Check if entry has existing adjustments
            if ($entry->adjustments()->count() > 0) {
                throw new InvalidArgumentException('Entry already has adjustments.');
            }

            // Check if entry is balanced
            if (!$this->hasImbalance($entry)) {
                return null; // No adjustment needed
            }

            $imbalanceAmount = $this->getImbalanceAmount($entry);
            $accountIds = $entry->lines()->pluck('account_id')->toArray();

            // Get or create adjustment account if not provided
            if (!$adjustmentAccountId) {
                $adjustmentAccountId = $this->getDefaultAdjustmentAccount($entry);
            }

            if (!$adjustmentAccountId) {
                throw new InvalidArgumentException('No adjustment account available or provided.');
            }

            // Ensure adjustment account is not already in the entry
            if (in_array($adjustmentAccountId, $accountIds)) {
                throw new InvalidArgumentException('Adjustment account already exists in the entry.');
            }

            // Determine adjustment type and line values
            $adjustmentType = 'debit_imbalance';
            $adjustmentDebit = '0.00';
            $adjustmentCredit = '0.00';

            if (bccomp($imbalanceAmount, '0', 2) > 0) {
                // More debits - need credit adjustment
                $adjustmentType = 'debit_imbalance';
                $adjustmentCredit = bcabs($imbalanceAmount, 2);
            } else {
                // More credits - need debit adjustment
                $adjustmentType = 'credit_imbalance';
                $adjustmentDebit = bcabs($imbalanceAmount, 2);
            }

            // Create adjustment entry as a new journal entry
            $adjustmentEntry = JournalEntry::create([
                'user_id' => $entry->user_id,
                'business_id' => $entry->business_id,
                'entry_date' => $entry->entry_date,
                'reference_type' => 'adjustment',
                'reference_id' => 'entry_' . $entry->id,
                'description' => 'Adjustment entry for ' . ($entry->description ? "'{$entry->description}'" : 'journal entry ' . $entry->id),
                'accounting_period_id' => $entry->accounting_period_id,
                'created_by' => $entry->created_by,
            ]);

            // Create adjustment line
            JournalEntryLine::create([
                'journal_entry_id' => $adjustmentEntry->id,
                'account_id' => $adjustmentAccountId,
                'debit_amount' => $adjustmentDebit,
                'credit_amount' => $adjustmentCredit,
                'description' => "Adjustment for {$adjustmentType}",
                'business_id' => $entry->business_id,
            ]);

            // Record the adjustment relationship
            $adjustment = JournalEntryAdjustment::create([
                'journal_entry_id' => $entry->id,
                'adjustment_entry_id' => $adjustmentEntry->id,
                'business_id' => $entry->business_id,
                'account_id' => $adjustmentAccountId,
                'debit_amount' => $adjustmentDebit,
                'credit_amount' => $adjustmentCredit,
                'adjustment_type' => $adjustmentType,
                'reason' => $reason ?? 'Automatically generated adjustment to balance entry',
                'is_applied' => false,
            ]);

            // Audit log
            try {
                JournalEntryAudit::create([
                    'journal_entry_id' => $entry->id,
                    'user_id' => $entry->user_id,
                    'business_id' => $entry->business_id,
                    'action' => 'adjustment_created',
                    'details' => [
                        'adjustment_entry_id' => $adjustmentEntry->id,
                        'adjustment_type' => $adjustmentType,
                        'imbalance_amount' => $imbalanceAmount,
                        'adjustment_account_id' => $adjustmentAccountId,
                    ],
                ]);
            } catch (\Exception $e) {
                report($e);
            }

            return $adjustment->fresh(['journalEntry', 'adjustmentEntry', 'account']);
        });
    }

    /**
     * Find or create the default adjustment account for a business
     * By convention, uses a dedicated "Adjustment" or "Suspense" account
     */
    public function getDefaultAdjustmentAccount(JournalEntry $entry): ?int
    {
        $businessId = $entry->business_id;
        $userId = $entry->user_id;

        // Try to find existing adjustment/suspense account
        $account = ChartofAccounts::withoutGlobalScope(\App\Models\Scopes\BusinessScope::class)
            ->where('user_id', $userId)
            ->where('business_id', $businessId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('account_name', 'LIKE', '%adjustment%')
                    ->orWhere('account_name', 'LIKE', '%suspense%')
                    ->orWhere('account_code', 'LIKE', '%adjustment%')
                    ->orWhere('account_code', 'LIKE', '%suspense%');
            })
            ->first();

        return $account?->id;
    }

    /**
     * Create adjustment entries for all imbalanced entries in a batch
     */
    public function createAdjustmentsForImbalancedEntries(
        int $businessId,
        ?int $adjustmentAccountId = null
    ): Collection {
        $adjustments = collect();

        $imbalancedEntries = JournalEntry::where('business_id', $businessId)
            ->doesntHave('adjustments')
            ->with('lines')
            ->get()
            ->filter(fn($entry) => $this->hasImbalance($entry));

        foreach ($imbalancedEntries as $entry) {
            try {
                $adjustment = $this->createAdjustmentEntry($entry, $adjustmentAccountId);
                if ($adjustment) {
                    $adjustments->push($adjustment);
                }
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $adjustments;
    }

    /**
     * Mark adjustment as applied (posted to ledger)
     */
    public function markAsApplied(JournalEntryAdjustment $adjustment): JournalEntryAdjustment
    {
        $adjustment->update(['is_applied' => true]);

        try {
            JournalEntryAudit::create([
                'journal_entry_id' => $adjustment->journal_entry_id,
                'user_id' => null,
                'business_id' => $adjustment->business_id,
                'action' => 'adjustment_applied',
                'details' => [
                    'adjustment_entry_id' => $adjustment->adjustment_entry_id,
                ],
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        return $adjustment;
    }

    /**
     * Verify that all accounts in the lines are unique (no duplicates)
     */
    public function validateNoDuplicateAccounts(JournalEntry $entry): bool
    {
        $accountIds = $entry->lines()->pluck('account_id')->toArray();
        $uniqueIds = array_unique($accountIds);
        
        return count($accountIds) === count($uniqueIds);
    }

    /**
     * Get all active adjustments for a journal entry
     */
    public function getActiveAdjustments(JournalEntry $entry): Collection
    {
        return $entry->adjustments()
            ->where('is_applied', false)
            ->with(['journalEntry', 'adjustmentEntry', 'account'])
            ->get();
    }

    /**
     * Get summary of adjustments for reporting
     */
    public function getAdjustmentSummary(JournalEntry $entry): array
    {
        $adjustments = $entry->adjustments;

        return [
            'has_adjustments' => $adjustments->count() > 0,
            'adjustment_count' => $adjustments->count(),
            'total_adjustment_amount' => $adjustments->sum(function ($adj) {
                return max($adj->debit_amount, $adj->credit_amount);
            }),
            'unapplied_count' => $adjustments->where('is_applied', false)->count(),
            'applied_count' => $adjustments->where('is_applied', true)->count(),
            'adjustments' => $adjustments,
        ];
    }
}
