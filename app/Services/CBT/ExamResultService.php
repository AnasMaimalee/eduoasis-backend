<?php
namespace App\Services\CBT;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\SubjectResult;
use App\Repositories\CBT\ExamResultRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamResultService
{
    public function __construct(
        protected ExamResultRepository $examResultRepository
    ) {}
    public function generate(Exam $exam): ExamResult
    {
        return DB::transaction(function () use ($exam) {

            $answers = $exam->answers()->with('question')->get();

            $totalQuestions = $answers->count();
            $totalCorrect = $answers->where('is_correct', true)->count();

            $result = ExamResult::create([
                'id'              => Str::uuid(),
                'exam_id'         => $exam->id,
                'user_id'         => $exam->user_id,
                'total_questions' => $totalQuestions,
                'total_correct'   => $totalCorrect,
                'started_at'      => $exam->created_at,
                'submitted_at'    => now(),
            ]);

            // Subject breakdown
            $answers
                ->groupBy('question.subject_id')
                ->each(function ($rows, $subjectId) use ($result) {
                    SubjectResult::create([
                        'id'               => Str::uuid(),
                        'exam_result_id'   => $result->id,
                        'subject_id'       => $subjectId,
                        'total_questions'  => $rows->count(),
                        'correct_answers'  => $rows->where('is_correct', true)->count(),
                    ]);
                });

            return $result;
        });
    }

    public function history(string $userId)
    {
        return $this->examResultRepository
            ->getUserResults($userId)
            ->map(function ($result) {
                return [
                    'exam_id'          => $result->exam_id,
                    'total_questions'  => $result->total_questions,
                    'total_correct'    => $result->total_correct,
                    'time_spent_seconds' =>
                        $result->submitted_at->diffInSeconds($result->started_at),

                    'subjects' => $result->subjectResults->map(fn ($s) => [
                        'name'  => $s->subject->name,
                        'score' => "{$s->correct_answers}/{$s->total_questions}",
                    ]),

                    'submitted_at' => $result->submitted_at->toDateTimeString(),
                ];
            });
    }


}
