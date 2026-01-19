<?php
namespace App\Models;

class ExamResult extends BaseModel
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'exam_id',
        'user_id',
        'total_questions',
        'total_correct',
        'started_at',
        'submitted_at',
    ];
    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
    public function subjectResults()
    {
        return $this->hasMany(SubjectResult::class);
    }
}
