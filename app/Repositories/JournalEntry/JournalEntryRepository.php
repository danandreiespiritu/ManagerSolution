<?php
namespace App\Repositories\JournalEntry;

use App\Models\JournalEntry as JournalEntryModel;
use App\Models\JournalEntryLine as LineModel;
use App\Services\AccountingPeriodGuard;
use App\Services\JournalEntryAdjustmentService;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class JournalEntryRepository implements IJournalEntryRepository
{
    public function __construct(
        protected JournalEntryAdjustmentService $adjustmentService
    ) {
    }

    public function create(array $payload): JournalEntryModel
    {
        return DB::transaction(function () use ($payload) {
            $lines = $payload['lines'] ?? [];
            $autoAdjust = $payload['auto_adjust'] ?? false;
            
            // enforce accounting period open
            $businessId = $payload['business_id'] ?? null;
            $periodId = $payload['accounting_period_id'] ?? null;
            $entryDate = $payload['entry_date'] ?? null;
            AccountingPeriodGuard::ensureOpen($businessId, $periodId, $entryDate);
            
            unset($payload['lines'], $payload['auto_adjust']);

            /** @var JournalEntryModel $entry */
            $entry = JournalEntryModel::create($payload);

            foreach ($lines as $ln) {
                $lnPayload = [
                    'journal_entry_id' => $entry->id,
                    'account_id' => $ln['account_id'] ?? null,
                    'account_code' => $ln['account_code'] ?? null,
                    'cash_category' => $ln['cash_category'] ?? null,
                    'description' => $ln['description'] ?? null,
                    'debit_amount' => $ln['debit_amount'] ?? 0,
                    'credit_amount' => $ln['credit_amount'] ?? 0,
                    'business_id' => $entry->business_id ?? null,
                ];

                LineModel::create($lnPayload);
            }

            // Refresh entry with lines
            $entry = $entry->fresh(['lines']);

            // Create adjustment entry if needed and auto_adjust is enabled
            if ($autoAdjust && $this->adjustmentService->hasImbalance($entry)) {
                try {
                    $this->adjustmentService->createAdjustmentEntry($entry);
                } catch (\Exception $e) {
                    report($e);
                    // Don't fail the entire operation if adjustment creation fails
                }
            }

            return $entry->fresh(['lines', 'adjustments']);
        });
    }

    public function getById(int $id): ?JournalEntryModel
    {
        return JournalEntryModel::with(['lines', 'lines.account', 'adjustments', 'adjustments.account'])
            ->find($id);
    }

    public function update(int $id, array $payload): JournalEntryModel
    {
        return DB::transaction(function () use ($id, $payload) {
            $lines = $payload['lines'] ?? [];
            $autoAdjust = $payload['auto_adjust'] ?? false;
            
            // enforce accounting period open for updates as well
            $businessId = $payload['business_id'] ?? null;
            $periodId = $payload['accounting_period_id'] ?? null;
            $entryDate = $payload['entry_date'] ?? null;
            AccountingPeriodGuard::ensureOpen($businessId, $periodId, $entryDate);
            
            unset($payload['lines'], $payload['auto_adjust']);

            /** @var JournalEntryModel $entry */
            $entry = JournalEntryModel::findOrFail($id);
            $entry->fill($payload);
            $entry->save();

            // Delete existing adjustment entries (since we're replacing the lines)
            $existingAdjustments = $entry->adjustments()->get();
            foreach ($existingAdjustments as $adjustment) {
                $adjustment->delete();
            }

            // Replace lines: delete existing and recreate
            LineModel::where('journal_entry_id', $entry->id)->delete();

            foreach ($lines as $ln) {
                $lnPayload = [
                    'journal_entry_id' => $entry->id,
                    'account_id' => $ln['account_id'] ?? null,
                    'account_code' => $ln['account_code'] ?? null,
                    'cash_category' => $ln['cash_category'] ?? null,
                    'description' => $ln['description'] ?? null,
                    'debit_amount' => $ln['debit_amount'] ?? 0,
                    'credit_amount' => $ln['credit_amount'] ?? 0,
                    'business_id' => $entry->business_id ?? null,
                ];

                LineModel::create($lnPayload);
            }

            // Refresh entry with lines
            $entry = $entry->fresh(['lines']);

            // Create adjustment entry if needed and auto_adjust is enabled
            if ($autoAdjust && $this->adjustmentService->hasImbalance($entry)) {
                try {
                    $this->adjustmentService->createAdjustmentEntry($entry);
                } catch (\Exception $e) {
                    report($e);
                }
            }

            return $entry->fresh(['lines', 'adjustments']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $entry = JournalEntryModel::find($id);
            if (! $entry) return false;
            
            // Delete adjustment entries
            $entry->adjustments()->delete();
            
            // Delete lines
            LineModel::where('journal_entry_id', $entry->id)->delete();
            
            return $entry->delete();
        });
    }

    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return JournalEntryModel::with(['lines', 'lines.account', 'adjustments'])
            ->where('user_id', $userId)
            ->orderByDesc('entry_date')
            ->paginate($perPage);
    }

    public function getAllForUser(int $userId): Collection
    {
        return JournalEntryModel::with(['lines', 'lines.account', 'adjustments'])
            ->where('user_id', $userId)
            ->orderByDesc('entry_date')
            ->get();
    }

    public function search(int $userId, ?string $search = null, int $perPage = 10)
    {
        $query = JournalEntryModel::with('lines')
            ->where('user_id', $userId)
            ->orderBy('entry_date', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_type', 'like', "%{$search}%")
                ->orWhere('reference_id', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->appends(['search' => $search]);
    }
}
