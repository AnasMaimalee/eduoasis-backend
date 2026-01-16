<?php

namespace App\Http\Controllers\Api\CBT\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;

class AdminCbtController extends Controller
{
    /**
     * Live CBT monitoring (SUPERADMIN only)
     */
    public function live(Request $request)
    {
        abort_unless($request->user()->hasRole('superadmin'), 403, 'Unauthorized');

        $exams = Exam::query()
            ->where('status', 'ongoing')
            ->with([
                'user:id,name,email'
            ])
            ->orderByDesc('started_at')
            ->get([
                'id',
                'user_id',
                'started_at',
                'ends_at',
                'last_seen_at',
                'status'
            ]);

        return response()->json([
            'message' => 'Live CBT sessions fetched',
            'data' => $exams
        ]);
    }
}
