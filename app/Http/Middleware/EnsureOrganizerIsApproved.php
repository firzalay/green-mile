<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizerIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === 'organizer' && $user->status !== 'approved') {
            abort(403, 'Your account is not approved yet.');
        }

        return $next($request);
    }
}
