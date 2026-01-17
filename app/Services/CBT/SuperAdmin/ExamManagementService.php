<?php

namespace App\Services\CBT\SuperAdmin;

use App\Models\Exam;
use App\Repositories\CBT\SuperAdmin\ExamAnalyticsRepository;
use App\Repositories\CBT\SuperAdmin\ExamManagementRepository;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamManagementService
{
    public function __construct(
        protected ExamManagementRepository $repo,
        protected ExamAnalyticsRepository $analyticsRepo,
    ) {}

    public function getAllExams(Request $request)
    {
        return $this->repo->fetchAll(
            status: $request->status,
            userId: $request->user_id
        );
    }

    public function getExamDetails(Exam $exam): array
    {
        return [
            'exam_id'     => $exam->id,
            'user'        => $exam->user,
            'status'      => $exam->status,
            'started_at'  => $exam->started_at,
            'submitted_at'=> $exam->submitted_at,
            'duration'    => $exam->started_at?->diffInSeconds($exam->submitted_at),
            'total_score' => $exam->total_score,
            'subjects'    => $exam->subjects,
        ];
    }

    public function getExamAnswers(Exam $exam)
    {
        return $this->repo->fetchAnswersWithQuestions($exam);
    }

    public function getScoreBreakdown(Exam $exam)
    {
        return $this->repo->scoreBreakdown($exam);
    }

    public function subjectAnalytics(Exam $exam)
    {
        return $this->analyticsRepo->subjectPerformance($exam);
    }

    public function exportResultPdf(Exam $exam)
    {
        return Pdf::loadView('pdf.exam-result', [
            'exam' => $exam,
            'answers' => $exam->answers()->with('question.subject')->get()
        ])->download("CBT-RESULT-{$exam->id}.pdf");
    }
    public function invalidateExam(Exam $exam, string $reason)
    {
        $exam->update([
            'status' => 'invalidated',
            'remark' => $reason,
            'total_score' => 0
        ]);
    }
    public function remarkExam(Exam $exam)
    {
        $score = 0;

        foreach ($exam->answers()->with('question')->get() as $answer) {
            if ($answer->selected_option === $answer->question->correct_option) {
                $score += config('cbt.mark_per_question');
            }
        }

        $exam->update([
            'total_score' => $score
        ]);

        return $score;
    }
    public function rankings()
    {
        return $this->repo->getRankings();
    }


    public function rankingsBySubject(string $subjectId)
    {
        return $this->repo->getRankingsBySubject($subjectId);
    }
}
