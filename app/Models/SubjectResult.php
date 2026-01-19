<?php

namespace App\Models;

class SubjectResult extends BaseModel
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'exam_result_id',
        'subject_id',
        'total_questions',
        'correct_answers',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
