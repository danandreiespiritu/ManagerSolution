<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BusinessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Apply only when a current business id is available.
        //
        // IMPORTANT: Models using this scope are expected to have a `business_id`
        // column. We intentionally do NOT silently skip scoping when the column
        // is missing, because that would create cross-business data leaks.
        $businessId = null;
        if (app()->bound('currentBusiness') && ($b = app('currentBusiness'))) {
            $businessId = $b->id ?? null;
        } elseif (session()->has('current_business_id')) {
            $businessId = session('current_business_id');
        }

        if ($businessId) {
            $builder->where($model->getTable().'.business_id', $businessId);
        } else {
            // No current business determined — deny by default to avoid accidental cross-business leaks.
            $builder->whereRaw('0 = 1');
        }
    }
}
