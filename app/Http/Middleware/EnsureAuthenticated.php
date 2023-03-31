<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->check())
            return redirect()->intended("/auth/login");
        $user = auth()->user();
        $user_balance = DB::select("SELECT * FROM balance_histories WHERE user_id = ? ORDER BY created_at DESC LIMIT 1", [$user->id]);
        $user->balance = $user_balance[0]->balance_after ?? 0;
        return $next($request);
    }
}