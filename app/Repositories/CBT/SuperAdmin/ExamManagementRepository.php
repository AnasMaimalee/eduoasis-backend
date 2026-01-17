<?php

namespace App\Repositories\CBT\SuperAdmin;

use App\Models\Exam;

class ExamManagementRepository
{
    public function fetchAll(?string $status, ?string $userId)
    {
        return Exam::with('user')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate(20);
    }

    public function fetchAnswersWithQuestions(Exam $exam)
    {
        return $exam->answers()
            ->with(['question.subject'])
            ->get()
            ->map(fn ($answer) => [
                'question' => $answer->question->question,
                'subject'  => $answer->question->subject->name,
                'selected' => $answer->selected_option,
                'correct'  => $answer->question->correct_option,
                'is_correct' => $answer->selected_option === $answer->question->correct_option,
                'mark' => $answer->selected_option === $answer->question->correct_option
                    ? config('cbt.mark_per_question')
                    : 0,
            ]);
    }

    public function scoreBreakdown(Exam $exam): array
    {
        $answers = $exam->answers()->with('question')->get();

        return [
            'total_questions' => $answers->count(),
            'correct' => $answers->where(
                fn ($a) => $a->selected_option === $a->question->correct_option
            )->count(),
            'wrong' => $answers->where(
                fn ($a) => $a->selected_option !== null &&
                    $a->selected_option !== $a->question->correct_option
            )->count(),
            'unanswered' => $answers->whereNull('selected_option')->count(),
            'total_score' => $exam->total_score
        ];
    }

    public function rankExamsByScore()
    {
        return \App\Models\Exam::where('status', 'submitted')
            ->orderByDesc('total_score')
            ->orderBy('submitted_at')
            ->get()
            ->values()
            ->map(fn ($exam, $index) => [
                'rank' => $index + 1,
                'user' => $exam->user->name,
                'score' => $exam->total_score,
                'exam_id' => $exam->id,
            ]);
    }
    public function getRankings()
    {
        return Exam::with('user')
            ->where('status', 'submitted')
            ->orderByDesc('total_score')
            ->orderBy('submitted_at') // tie breaker
            ->get()
            ->values()
            ->map(function ($exam, $index) {
                return [
                    'rank'        => $index + 1,
                    'exam_id'     => $exam->id,
                    'user_id'     => $exam->user_id,
                    'user_name'   => $exam->user->name,
                    'score'       => $exam->total_score,
                    'submitted_at'=> $exam->submitted_at,
                ];
            });
    }

    public function getRankingsBySubject(string $subjectId)
    {
        return Exam::with('user')
            ->whereHas('answers.question', function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->where('status', 'submitted')
            ->orderByDesc('total_score')
            ->orderBy('submitted_at') // tie breaker
            ->get()
            ->values()
            ->map(function ($exam, $index) {
                return [
                    'rank'      => $index + 1,
                    'exam_id'   => $exam->id,
                    'user_id'   => $exam->user_id,
                    'user_name' => $exam->user->name,
                    'score'     => $exam->total_score,
                ];
            });
    }
}
