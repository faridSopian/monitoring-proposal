<?php

namespace App\Http\Middleware;

use App\Models\AuditTrail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAuditTrail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) { // Memastikan user sudah login
            $user = Auth::user();
            $username = $user->username ?? $user->name ?? $user->email; // Gunakan alternatif jika username kosong

            AuditTrail::create([
                'user_id' => $user->id,
                'username' => $username, // Pastikan `username` tidak null
                'menu_accessed' => $request->path(),
                'method' => $request->method(),
                'activity_time' => now(),
                'detail' => json_encode($request->all()),
            ]);
        }

        return $next($request);
    }
}
