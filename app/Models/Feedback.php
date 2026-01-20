<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends BaseModel
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'feedback';

    protected $fillable = [
        'full_name',
        'email',
        'message',
        'ip_address',
        'status',
    ];


    protected $casts = [
        'full_name' => 'string',
        'email' => 'string',
        'message' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id ??= (string) \Illuminate\Support\Str::uuid();
        });
    }
}
