<?php

namespace App\Http\Middleware;

use App\Models\BillingStatus;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AppRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
//        if (isset($user) && $user->id == 100) {
        $billing_status = BillingStatus::where('user_id', $user->id)->first();
        if (isset($billing_status) && $billing_status->billing_status == 0) {
            return Redirect::tokenRedirect('plans');
        }
//        }

        return $next($request);
    }
}
