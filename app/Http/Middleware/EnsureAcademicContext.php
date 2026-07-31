<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAcademicContext
{
    public function handle(Request $request, Closure $next)
    {
        $currentSession = getCurrentSession();
        $currentTerm = getCurrentTerm();

        if (!$currentSession) {
            return redirect()->back()->with([
                'message' => 'Please set current academic session first.',
                'alert-type' => 'error',
            ]);
        }

        $request->attributes->set('currentSession', $currentSession);
        $request->attributes->set('currentTerm', $currentTerm);

        return $next($request);
    }
}
