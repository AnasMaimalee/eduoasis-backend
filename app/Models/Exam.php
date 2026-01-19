<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExamResult;
class Exam extends BaseModel
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'status',
        'started_at',
        'ends_at',
        'submitted_at',
        'last_seen_at',
        'duration_minutes',
        'total_questions',
        'total_score',
        'submitted'
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'ends_at'      => 'datetime',
        'submitted_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'submitted'    => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id ??= (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }
    public function result()
    {
        return $this->hasOne(ExamResult::class);
    }
}
