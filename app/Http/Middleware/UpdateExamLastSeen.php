<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Exam;

class UpdateExamLastSeen
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            Exam::where('user_id', auth()->id())
                ->where('status', 'ongoing')
                ->update([
                    'last_seen_at' => now()
                ]);
        }

        return $next($request);
    }
}
