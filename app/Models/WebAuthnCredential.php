<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class WebAuthnCredential extends Model
{
    protected $table = 'webauthn_credentials'; // Your existing table
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'authenticatable_type', 
        'authenticatable_id',
        'credential_id',
        'alias',
        'counter', 
        'rp_id'
    ];

    protected $casts = [
        'counter' => 'integer',
    ];

    protected $hidden = ['public_key'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'authenticatable_id');
    }
}
