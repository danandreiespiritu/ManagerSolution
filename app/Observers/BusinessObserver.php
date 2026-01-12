<?php

namespace App\Observers;

use App\Models\Business;
use App\Services\StandardCoaService;
use Illuminate\Support\Facades\Log;

class BusinessObserver
{
    public function __construct(protected StandardCoaService $standardCoa)
    {
    }

    public function created(Business $business): void
    {
        try {
            if (!$business->id || !$business->user_id) {
                return;
            }

            $this->standardCoa->ensureForBusiness((int) $business->user_id, (int) $business->id);
        } catch (\Throwable $e) {
            // Do not block business creation if seeding fails; log for investigation.
            Log::error('Standard COA seeding failed for new business', [
                'business_id' => $business->id,
                'user_id' => $business->user_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
