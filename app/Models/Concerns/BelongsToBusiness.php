<?php

namespace App\Models\Concerns;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToBusiness
{
    public static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope(new BusinessScope());

        $resolveBusinessId = static function (): ?int {
            if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
                return $b->id ?? null;
            }

            if (session()->has('current_business_id')) {
                return (int) session('current_business_id');
            }

            return null;
        };

        static::creating(function (Model $model) use ($resolveBusinessId) {
            $businessId = ($resolveBusinessId)();

            if ($businessId) {
                // Always enforce the current business on create.
                $model->business_id = $businessId;
            }
        });

        static::updating(function (Model $model) use ($resolveBusinessId) {
            // Disallow moving a record across businesses.
            if ($model->isDirty('business_id')) {
                $model->business_id = $model->getOriginal('business_id');
            }

            // Also enforce current business context if available.
            $businessId = ($resolveBusinessId)();
            if ($businessId && (int) $model->business_id !== (int) $businessId) {
                $model->business_id = $businessId;
            }
        });
    }
}
