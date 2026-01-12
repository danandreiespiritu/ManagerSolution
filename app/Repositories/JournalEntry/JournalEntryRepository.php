<?php
namespace App\Repositories\JournalEntry;

use App\Models\JournalEntry as JournalEntryModel;
use App\Models\JournalEntryLine as LineModel;
use App\Services\AccountingPeriodGuard;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class JournalEntryRepository implements IJournalEntryRepository
{
    public function create(array $payload): JournalEntryModel
    {
        return DB::transaction(function () use ($payload) {
            $lines = $payload['lines'] ?? [];
            // enforce accounting period open
            $businessId = $payload['business_id'] ?? null;
            $periodId = $payload['accounting_period_id'] ?? null;
            $entryDate = $payload['entry_date'] ?? null;
            AccountingPeriodGuard::ensureOpen($businessId, $periodId, $entryDate);
            unset($payload['lines']);

            /** @var JournalEntryModel $entry */
            $entry = JournalEntryModel::create($payload);

            foreach ($lines as $ln) {
                $lnPayload = [
                    'journal_entry_id' => $entry->id,
                    'account_id' => $ln['account_id'] ?? null,
                    'description' => $ln['description'] ?? null,
                    'debit_amount' => $ln['debit_amount'] ?? 0,
                    'credit_amount' => $ln['credit_amount'] ?? 0,
                    'customer_id' => $ln['customer_id'] ?? null,
                    'supplier_id' => $ln['supplier_id'] ?? null,
                    'business_id' => $entry->business_id ?? null,
                ];

                LineModel::create($lnPayload);
            }

            return $entry->fresh(['lines']);
        });
    }

    public function getById(int $id): ?JournalEntryModel
    {
        return JournalEntryModel::with(['lines', 'lines.account'])->find($id);
    }

    public function update(int $id, array $payload): JournalEntryModel
    {
        return DB::transaction(function () use ($id, $payload) {
            $lines = $payload['lines'] ?? [];
            // enforce accounting period open for updates as well
            $businessId = $payload['business_id'] ?? null;
            $periodId = $payload['accounting_period_id'] ?? null;
            $entryDate = $payload['entry_date'] ?? null;
            AccountingPeriodGuard::ensureOpen($businessId, $periodId, $entryDate);
            unset($payload['lines']);

            /** @var JournalEntryModel $entry */
            $entry = JournalEntryModel::findOrFail($id);
            $entry->fill($payload);
            $entry->save();

            // Replace lines: delete existing and recreate
            LineModel::where('journal_entry_id', $entry->id)->delete();

            foreach ($lines as $ln) {
                $lnPayload = [
                    'journal_entry_id' => $entry->id,
                    'account_id' => $ln['account_id'] ?? null,
                    'description' => $ln['description'] ?? null,
                    'debit_amount' => $ln['debit_amount'] ?? 0,
                    'credit_amount' => $ln['credit_amount'] ?? 0,
                    'customer_id' => $ln['customer_id'] ?? null,
                    'supplier_id' => $ln['supplier_id'] ?? null,
                    'business_id' => $entry->business_id ?? null,
                ];

                LineModel::create($lnPayload);
            }

            return $entry->fresh(['lines']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $entry = JournalEntryModel::find($id);
            if (! $entry) return false;
            LineModel::where('journal_entry_id', $entry->id)->delete();
            return $entry->delete();
        });
    }

    public function paginate(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return JournalEntryModel::with(['lines', 'lines.account'])->where('user_id', $userId)
            ->orderByDesc('entry_date')
            ->paginate($perPage);
    }

    public function getAllForUser(int $userId): Collection
    {
        return JournalEntryModel::with(['lines', 'lines.account'])->where('user_id', $userId)->orderByDesc('entry_date')->get();
    }
}
