<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use Illuminate\Validation\ValidationException;

class AccountingPeriodGuard
{
    /**
     * Ensure the accounting period is open for the given business/period/date.
     * Throws ValidationException when closed or not found.
     *
     * @param int|null $businessId
     * @param int|null $periodId
     * @param string|null $entryDate (Y-m-d)
     * @return void
     */
    public static function ensureOpen(?int $businessId = null, ?int $periodId = null, ?string $entryDate = null): void
    {
        if ($periodId) {
            $period = AccountingPeriod::find($periodId);
            if (! $period) {
                throw ValidationException::withMessages(['accounting_period_id' => 'Selected accounting period not found.']);
            }
            if ($period->is_closed) {
                throw ValidationException::withMessages(['accounting_period_id' => 'Accounting period must be OPEN.']);
            }
            if ($businessId && $period->business_id != $businessId) {
                throw ValidationException::withMessages(['accounting_period_id' => 'Selected accounting period does not belong to the selected business.']);
            }
            // if an entry date was provided, ensure it falls within the selected period (inclusive)
            if ($entryDate) {
                try {
                    $ed = \Carbon\Carbon::parse($entryDate)->startOfDay();
                    $start = $period->start_date->startOfDay();
                    $end = $period->end_date->endOfDay();
                    if (! $ed->betweenIncluded($start, $end)) {
                        throw ValidationException::withMessages(['entry_date' => 'Entry date must be within the selected accounting period.']);
                    }
                } catch (\Exception $e) {
                    throw ValidationException::withMessages(['entry_date' => 'Invalid entry date.']);
                }
            }
            return;
        }

        // no explicit period provided: require an open period covering the date if business is provided
        if ($businessId && $entryDate) {
            $found = AccountingPeriod::where('business_id', $businessId)
                ->where('start_date', '<=', $entryDate)
                ->where('end_date', '>=', $entryDate)
                ->where('is_closed', false)
                ->exists();

            if (! $found) {
                throw ValidationException::withMessages(['accounting_period_id' => 'Accounting period must be OPEN for the entry date.']);
            }
        }
    }
}
