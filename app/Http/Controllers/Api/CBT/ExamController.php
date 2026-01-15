<?php

namespace App\Http\Controllers\Api\CBT;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use App\Services\CBT\ExamService;
use App\Services\CBT\WalletPaymentService;
use Illuminate\Support\Str;
use App\Notifications\ExamStartedNotification;

class ExamController extends Controller
{
    public function __construct(
        protected ExamService $service,
        protected WalletPaymentService $walletService
    ) {}

    // ---------------- START EXAM ----------------


    public function start(Request $request, WalletPaymentService $walletService, ExamService $examService)
    {
        $request->validate([
            'subjects' => 'required|array|size:' . config('cbt.subjects_count'),
            'subjects.*' => 'exists:subjects,id',
        ]);

        $user = $request->user();
        $examFee = (float) config('cbt.exam_fee');

        if ($examFee <= 0) {
            return response()->json([
                'message' => 'Invalid exam fee configuration'
            ], 500);
        }

        // ✅ START EXAM PROPERLY (THIS CREATES QUESTIONS)
        $exam = $examService->startExam(
            $user->id,
            $request->subjects
        );

        // ✅ DEBIT WALLET AFTER EXAM IS CREATED
        $walletService->debitExamFee(
            $user->id,
            $exam,
            $examFee
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Exam started successfully',
            'data' => $exam
        ]);
    }



    // ---------------- FETCH QUESTIONS ----------------
    public function show(Exam $exam)
    {
        $questions = $this->service->getExamQuestions($exam);

        return response()->json([
            'message' => 'Questions fetched',
            'questions' => $questions
        ]);
    }

    // ---------------- SAVE ANSWER ----------------
    public function submitAnswer(Request $request, Exam $exam, $answerId)
    {
        // ✅ Validate ONLY user input
        $validated = $request->validate([
            'selected_option' => 'required|in:A,B,C,D,E',
        ]);

        // ✅ Fetch answer using route param
        $examAnswer = ExamAnswer::where('id', $answerId)
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        $examAnswer->update([
            'selected_option' => $validated['selected_option'],
            'is_correct' => $examAnswer->question->correct_option === $validated['selected_option'],
        ]);

        return response()->json([
            'message' => 'Answer saved successfully'
        ]);
    }


    // ---------------- SUBMIT EXAM ----------------
    public function submit(Exam $exam)
    {
        $this->service->submitExam($exam);

        return response()->json([
            'message' => 'Exam submitted successfully'
        ]);
    }

    // ---------------- HANDLE NETWORK FAILURE REFUND ----------------
    public function refundIfUnsubmitted(Exam $exam)
    {
        try {
            $this->walletService->refundExamFee($exam);
            return response()->json([
                'message' => 'Exam fee refunded due to network issue'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to refund: ' . $e->getMessage()
            ], 400);
        }
    }
}
