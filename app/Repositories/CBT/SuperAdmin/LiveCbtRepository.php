<?php

namespace App\Repositories\CBT\SuperAdmin;

use App\Models\ExamSession;

class LiveCbtRepository
{
    public function getLiveSessions()
    {
        return ExamSession::with([
            'user:id,name,email',
            'exam.subjects:id,name',
            'exam.answers:id,exam_id,selected_option'
        ])
            ->where('is_submitted', false)
            ->latest('starts_at')
            ->get()
            ->map(function ($session) {

                $answers = $session->exam->answers;

                $answered = $answers
                    ->whereNotNull('selected_option')
                    ->count();

                $total = $answers->count();

                return [
                    'session_id' => $session->id,
                    'exam_id' => $session->exam->id,
                    'user' => $session->user,
                    'started_at' => $session->starts_at,
                    'ends_at' => $session->ends_at,
                    'remaining_seconds' => max(
                        0,
                        now()->diffInSeconds($session->ends_at, false)
                    ),
                    'total_questions' => $total,
                    'answered_questions' => $answered,
                    'unanswered_questions' => $total - $answered,
                    'subjects' => $session->exam->subjects,
                ];
            });
    }
}
