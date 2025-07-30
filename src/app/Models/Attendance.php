<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function correctionApplications(): HasMany
    {
        return $this->hasMany(CorrectionApplication::class);
    }

    public function latestCorrectionApplication(): HasOne
    {
        return $this->hasOne(CorrectionApplication::class)->latestOfMany();
    }
}
