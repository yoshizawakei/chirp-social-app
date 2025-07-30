<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',

    ];

    /**
     * ユーザーが管理者かどうかを確認する
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 2; // 管理者の役割は2
    }

    public function isUser(): bool
    {
        return $this->role === 1; // 一般ユーザーの役割は1
    }


    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function correctionApplications(): HasMany
    {
        return $this->hasMany(CorrectionApplication::class);
    }
}
