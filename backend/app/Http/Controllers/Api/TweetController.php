<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TweetController extends Controller
{
    /**
     * 投稿の一覧表示
     */
    public function index(): JsonResponse
    {
        $tweets = Tweet::withCount('likes')
            ->with([
                'comments' => function ($query) {
                    $query->latest()->limit(5);
                }
            ])
            ->latest()
            ->get();

        return response()->json([
            'tweets' => $tweets
        ]);
    }

    /**
     * 投稿の追加処理 (POST)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:120'],
            'user_id' => ['required', 'string'],
            'user_name' => ['required', 'string', 'max:20'],
        ]);

        $tweet = Tweet::create($validated);

        return response()->json([
            'message' => '投稿が追加されました。',
            'tweet' => $tweet
        ], 201);
    }

    /**
     * 投稿の削除処理 (DELETE)
     */
    public function destroy(Tweet $tweet): JsonResponse
    {
        // 🚨 **本来はここで「この投稿を削除する権限があるか（投稿者本人か）」をチェックする必要がありますが、今回は一旦スキップします。**

        $tweet->delete();

        return response()->json([
            'message' => '投稿が削除されました。'
        ]);
    }
}