<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Log;

class SetCurrentBusiness
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            $business = null;

            // If session has a selected business, try to load it
            $selected = $request->session()->get('current_business_id');
            if ($selected) {
                $business = Business::where('id', $selected)->where('user_id', $user->id)->first();

                // If the selected business doesn't exist (or isn't accessible), clear it
                // to avoid poisoning subsequent writes with an invalid business_id.
                if (! $business) {
                    $request->session()->forget('current_business_id');
                    $selected = null;
                }
            }

            // Fallback to first business for the user
            if (! $business) {
                $business = Business::where('user_id', $user->id)->first();
                if ($business) {
                    $request->session()->put('current_business_id', $business->id);
                }
            }

            if ($business) {
                app()->instance('currentBusiness', $business);
                Log::debug('SetCurrentBusiness middleware bound currentBusiness', ['user_id' => $user->id, 'business_id' => $business->id]);
                // share with views
                if (function_exists('view')) {
                    view()->share('currentBusiness', $business);
                }
            } else {
                Log::debug('SetCurrentBusiness middleware did not find business for user', ['user_id' => $user->id, 'selected' => $selected]);
            }
        }

        return $next($request);
    }
}
