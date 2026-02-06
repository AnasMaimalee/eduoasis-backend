<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Visitor extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });

    }
    
   protected $fillable = [
        'id',
        'ip',
        'browser',
        'os',
        'device',
        'referrer',
        'country',
        'city',
    ];
}

