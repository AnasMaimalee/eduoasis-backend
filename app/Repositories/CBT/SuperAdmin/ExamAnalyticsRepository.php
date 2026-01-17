<?php

namespace App\Repositories\CBT\SuperAdmin;

use App\Models\Exam;

class ExamAnalyticsRepository
{
    public function subjectPerformance(Exam $exam)
    {
        return $exam->answers()
            ->with('question.subject')
            ->get()
            ->groupBy(fn ($a) => $a->question->subject->name)
            ->map(function ($answers, $subject) {
                $total = $answers->count();
                $correct = $answers->where(
                    fn ($a) => $a->selected_option === $a->question->correct_option
                )->count();

                return [
                    'subject' => $subject,
                    'total_questions' => $total,
                    'correct' => $correct,
                    'wrong' => $total - $correct,
                    'score' => $correct * config('cbt.mark_per_question'),
                ];
            })
            ->values();
    }
}
