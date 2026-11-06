<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        if (! $user) {
            $login = ['customer' => 'login', 'staff' => 'sim.login', 'owner' => 'eksekutif.login'][$role] ?? 'login';
            return redirect()->guest(route($login));
        }
        abort_unless($user->role === $role, 403, 'Akun Anda tidak memiliki akses ke halaman ini.');
        return $next($request);
    }
}
