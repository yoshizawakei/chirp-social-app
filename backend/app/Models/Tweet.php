<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'content',
    ];

    /**
     * この投稿に対するコメントを取得
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * この投稿に対するいいねを取得
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}