<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CbtSetting extends Model
{
    use HasFactory;

    protected $table = 'cbt_settings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'subjects_count',
        'questions_per_subject',
        'duration_minutes',
        'exam_fee',
    ];

    protected $casts = [
        'subjects_count' => 'integer',
        'questions_per_subject' => 'integer',
        'duration_minutes' => 'integer',
        'exam_fee' => 'float',
    ];
}
