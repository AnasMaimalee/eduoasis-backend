<?php
namespace App\Repositories\CBT;

use App\Models\ExamResult;

class ExamResultRepository
{
    public function getUserResults(string $userId)
    {
        return ExamResult::with(['subjectResults.subject'])
            ->where('user_id', $userId)
            ->orderByDesc('submitted_at')
            ->get();
    }
}
