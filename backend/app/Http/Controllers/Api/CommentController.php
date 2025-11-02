<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * 特定の投稿のコメント一覧表示
     */
    public function index(Tweet $tweet): JsonResponse
    {
        $comments = $tweet->comments()->latest()->get();

        return response()->json([
            'comments' => $comments,
            'tweet' => $tweet
        ]);
    }

    /**
     * コメントの追加処理 (POST)
     */
    public function store(Request $request, Tweet $tweet): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:120'],
            'user_id' => ['required', 'string'],
            'user_name' => ['required', 'string', 'max:20'],
        ]);

        $comment = $tweet->comments()->create($validated);

        return response()->json([
            'message' => 'コメントが追加されました。',
            'comment' => $comment
        ], 201);
    }

    // 💡 **コメント削除が必要であれば、ここに destroy メソッドを追加します。**
}