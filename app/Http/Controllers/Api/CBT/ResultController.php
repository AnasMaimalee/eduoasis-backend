<?php

namespace App\Http\Controllers\Api\CBT;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Services\CBT\ResultService;
use App\Services\CBT\ResultPdfService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResultController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ResultService $resultService,
        protected ResultPdfService $pdfService
    ) {}

    /**
     * Show full result with breakdown
     */
    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);

        $result = $this->resultService->getResult($exam);

        return response()->json([
            'message' => 'Result fetched successfully',
            'data' => $result,
        ]);
    }

    /**
     * Summary for dashboard / history
     */
    public function summary(Exam $exam)
    {
        $this->authorize('view', $exam);

        $summary = $this->resultService->summary($exam);

        return response()->json([
            'message' => 'Summary fetched successfully',
            'data' => $summary,
        ]);
    }

    /**
     * Generate PDF result slip
     */
    public function downloadResult(Exam $exam, ResultPdfService $pdfService)
    {
        // 🔐 Ensure exam belongs to authenticated user


        // ✅ Build subject breakdown EXACTLY as PDF expects
        $subjects = $exam->answers()
            ->with('question.subject')
            ->get()
            ->groupBy(fn ($ans) => $ans->question->subject->name)
            ->map(function ($items, $subject) {
                $total = $items->count();
                $correct = $items->where('is_correct', true)->count();

                return [
                    'subject' => $subject,
                    'total_questions' => $total,
                    'correct' => $correct,
                    'wrong' => $total - $correct,
                    'score' => $correct, // adjust later if subject has weight
                ];
            })
            ->values()
            ->toArray();

        $breakdown = [
            'subjects' => $subjects,
        ];

        // ✅ Generate & download PDF
        return $pdfService->generate($exam, $breakdown);
    }


}
