<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "attendance_id",
        "clock_in_time_before",
        "clock_out_time_before",
        "rests_before",
        "notes_before",
        "clock_in_time_after",
        "clock_out_time_after",
        "rests_after",
        "notes_after",
        "is_approved"
    ];
    protected $casts = [
        "rests_before" => "array",
        "rests_after" => "array",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
