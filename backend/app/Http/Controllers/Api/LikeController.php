<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    /**
     * いいねの追加 (POST)
     */
    public function store(Request $request, Tweet $tweet): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string'],
        ]);

        // 既にいいねしているかチェック
        $existingLike = $tweet->likes()->where('user_id', $validated['user_id'])->first();

        if ($existingLike) {
            return response()->json([
                'message' => '既にお気に入りに追加されています。'
            ], 409); // Conflict
        }

        // いいねを作成
        $like = $tweet->likes()->create([
            'user_id' => $validated['user_id']
        ]);

        // 更新されたいいね数を返す
        $likeCount = $tweet->likes()->count();

        return response()->json([
            'message' => 'いいねが追加されました。',
            'like' => $like,
            'like_count' => $likeCount
        ], 201);
    }

    /**
     * いいねの削除 (DELETE)
     */
    public function destroy(Request $request, Tweet $tweet): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string'],
        ]);

        $deleted = $tweet->likes()
            ->where('user_id', $validated['user_id'])
            ->delete();

        if ($deleted === 0) {
            return response()->json([
                'message' => 'いいねは見つかりませんでした。'
            ], 404);
        }

        // 更新されたいいね数を返す
        $likeCount = $tweet->likes()->count();

        return response()->json([
            'message' => 'いいねが削除されました。',
            'like_count' => $likeCount
        ]);
    }
}