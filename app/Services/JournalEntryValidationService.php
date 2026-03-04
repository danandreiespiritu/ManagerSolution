<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service for validating and reporting on journal entries with adjustments
 */
class JournalEntryValidationService
{
    public function __construct(
        protected JournalEntryAdjustmentService $adjustmentService
    ) {
    }

    /**
     * Validate that a journal entry is balanced including adjustments
     */
    public function isBalancedWithAdjustments(JournalEntry $entry): bool
    {
        $totalDebit = $entry->getTotalDebitsIncludingAdjustments();
        $totalCredit = $entry->getTotalCreditsIncludingAdjustments();

        return bccomp((string)$totalDebit, (string)$totalCredit, 2) === 0;
    }

    /**
     * Get all lines for an entry including adjustment lines
     */
    public function getAllLines(JournalEntry $entry): Collection
    {
        return $entry->getAllLines();
    }

    /**
     * Get detailed balance information including adjustments
     */
    public function getBalanceDetails(JournalEntry $entry): array
    {
        $originalDebit = $entry->lines()->sum('debit_amount');
        $originalCredit = $entry->lines()->sum('credit_amount');
        $originalImbalance = bcsub((string)$originalDebit, (string)$originalCredit, 2);

        $adjustmentDebit = 0;
        $adjustmentCredit = 0;
        foreach ($entry->adjustments as $adj) {
            $adjustmentDebit += $adj->debit_amount;
            $adjustmentCredit += $adj->credit_amount;
        }

        $finalDebit = bcadd((string)$originalDebit, (string)$adjustmentDebit, 2);
        $finalCredit = bcadd((string)$originalCredit, (string)$adjustmentCredit, 2);
        $finalImbalance = bcsub((string)$finalDebit, (string)$finalCredit, 2);

        return [
            'original_debit' => $originalDebit,
            'original_credit' => $originalCredit,
            'original_imbalance' => $originalImbalance,
            'adjustment_debit' => $adjustmentDebit,
            'adjustment_credit' => $adjustmentCredit,
            'final_debit' => $finalDebit,
            'final_credit' => $finalCredit,
            'final_imbalance' => $finalImbalance,
            'is_balanced' => bccomp($finalImbalance, '0', 2) === 0,
            'has_adjustments' => $entry->adjustments->count() > 0,
        ];
    }

    /**
     * Validate all accounts are unique in a journal entry
     */
    public function hasUniqueAccounts(JournalEntry $entry): bool
    {
        return $this->adjustmentService->validateNoDuplicateAccounts($entry);
    }

    /**
     * Get all unique accounts across original and adjustment lines
     */
    public function getUniqueAccountsWithAdjustments(JournalEntry $entry): Collection
    {
        return $entry->getAllLines()
            ->pluck('account_id')
            ->unique()
            ->values();
    }

    /**
     * Check if entry has any unapplied adjustments
     */
    public function hasUnappliedAdjustments(JournalEntry $entry): bool
    {
        return $entry->adjustments()
            ->where('is_applied', false)
            ->exists();
    }

    /**
     * Mark all unapplied adjustments as applied
     */
    public function applyAllAdjustments(JournalEntry $entry): int
    {
        $count = 0;
        foreach ($entry->adjustments()->where('is_applied', false)->get() as $adjustment) {
            $this->adjustmentService->markAsApplied($adjustment);
            $count++;
        }
        return $count;
    }

    /**
     * Get summary statistics for reporting
     */
    public function getSummaryForEntry(JournalEntry $entry): array
    {
        $balanceDetails = $this->getBalanceDetails($entry);
        $adjustmentSummary = $this->adjustmentService->getAdjustmentSummary($entry);

        return [
            'entry_id' => $entry->id,
            'entry_date' => $entry->entry_date,
            'description' => $entry->description,
            'balance_details' => $balanceDetails,
            'adjustments' => $adjustmentSummary,
            'is_valid' => $balanceDetails['is_balanced'] && $this->hasUniqueAccounts($entry),
        ];
    }

    /**
     * Validate entries for export/reporting (must be balanced with unique accounts)
     */
    public function validateForReporting(JournalEntry $entry): array
    {
        $errors = [];

        if (!$this->isBalancedWithAdjustments($entry)) {
            $errors[] = "Entry {$entry->id} is not balanced even with adjustments";
        }

        if (!$this->hasUniqueAccounts($entry)) {
            $errors[] = "Entry {$entry->id} has duplicate accounts";
        }

        return $errors;
    }

    /**
     * Batch validate multiple entries
     */
    public function validateMultipleForReporting(Collection $entries): array
    {
        $results = [];
        foreach ($entries as $entry) {
            $errors = $this->validateForReporting($entry);
            if (count($errors) > 0) {
                $results[$entry->id] = $errors;
            }
        }
        return $results;
    }
}
