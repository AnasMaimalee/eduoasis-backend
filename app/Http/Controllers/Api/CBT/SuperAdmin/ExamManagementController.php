<?php

namespace App\Http\Controllers\Api\CBT\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Services\CBT\SuperAdmin\ExamManagementService;
use Illuminate\Http\Request;

class ExamManagementController extends Controller
{
    public function __construct(
        protected ExamManagementService $service
    ) {}

    /**
     * 📊 All exams (filterable)
     */
    public function index(Request $request)
    {
        $exams = $this->service->getAllExams($request);

        return response()->json([
            'message' => 'All exams fetched',
            'data' => $exams
        ]);
    }

    /**
     * 👤 Single user exam summary
     */
    public function show(Exam $exam)
    {
        return response()->json([
            'message' => 'Exam details fetched',
            'data' => $this->service->getExamDetails($exam)
        ]);
    }

    /**
     * ❓ Questions + answers + marks
     */
    public function answers(Exam $exam)
    {
        return response()->json([
            'message' => 'Exam answers fetched',
            'data' => $this->service->getExamAnswers($exam)
        ]);
    }

    /**
     * 🧮 Score breakdown
     */
    public function score(Exam $exam)
    {
        return response()->json([
            'message' => 'Score breakdown',
            'data' => $this->service->getScoreBreakdown($exam)
        ]);
    }
    public function analytics(Exam $exam)
    {
        return response()->json([
            'message' => 'Subject analytics',
            'data' => $this->service->subjectAnalytics($exam)
        ]);
    }
    public function rankings()
    {
        return response()->json([
            'message' => 'CBT rankings',
            'data' => $this->service->rankings()
        ]);
    }
    public function examPdf(Exam $exam)
    {
        return $this->service->exportResultPdf($exam);
    }
    public function invalidate(Request $request, Exam $exam)
    {
        $request->validate([
            'reason' => 'required|string|min:10'
        ]);

        $this->service->invalidateExam($exam, $request->reason);

        return response()->json([
            'message' => 'Exam invalidated successfully'
        ]);
    }
    public function remark(Exam $exam)
    {
        $newScore = $this->service->remarkExam($exam);

        return response()->json([
            'message' => 'Exam re-marked successfully',
            'new_score' => $newScore
        ]);
    }

    public function rankingsBySubject(string $subjectId)
    {
        return response()->json([
            'message' => 'Subject rankings fetched',
            'data' => $this->service->rankingsBySubject($subjectId)
        ]);
    }

}
