<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tweet_id',
        'user_id',
        'user_name',
        'content',
    ];

    /**
     * このコメントが属する投稿を取得
     */
    public function tweet(): BelongsTo
    {
        return $this->belongsTo(Tweet::class);
    }
}