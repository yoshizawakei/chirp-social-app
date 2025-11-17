<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // 投稿一覧（コメント数・いいね数も含めて返す）
    public function index()
    {
        $posts = Post::with(['comments', 'likes'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'userId' => $post->user_id,
                    'username' => $post->username,
                    'message' => $post->message,
                    'createdAt' => $post->created_at,
                    'likes' => $post->likes->pluck('user_id')->values(),
                    'likeCount' => $post->likes->count(),
                    'commentsCount' => $post->comments->count(),
                ];
            });

        return response()->json($posts);
    }

    // 投稿詳細（コメント数・いいね数付き）
    public function show($id)
    {
        $post = Post::with(['comments', 'likes'])->findOrFail($id);

        return response()->json([
            'id' => $post->id,
            'userId' => $post->user_id,
            'username' => $post->username,
            'message' => $post->message,
            'createdAt' => $post->created_at,
            'likes' => $post->likes->pluck('user_id')->values(),
            'likeCount' => $post->likes->count(),
            'commentsCount' => $post->comments->count(),
        ]);
    }

    // 新規投稿
    public function store(Request $request)
    {
        $data = $request->validate([
            'userId' => 'required|string',
            'username' => 'required|string|max:50',
            'message' => 'required|string|max:120',
        ]);

        $post = Post::create([
            'user_id' => $data['userId'],
            'username' => $data['username'],
            'message' => $data['message'],
        ]);

        return response()->json($post, 201);
    }

    // 投稿削除（投稿者本人のみ）
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'userId' => 'required|string',
        ]);

        $post = Post::findOrFail($id);

        if ($post->user_id !== $request->input('userId')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post->delete();
        return response()->json(['message' => 'deleted']);
    }

    // いいねトグル
    public function toggleLike(Request $request, $id)
    {
        $data = $request->validate([
            'userId' => 'required|string',
        ]);

        $post = Post::findOrFail($id);

        $existing = $post->likes()->where('user_id', $data['userId'])->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            $post->likes()->create(['user_id' => $data['userId']]);
            $liked = true;
        }

        $likeCount = $post->likes()->count();

        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount,
        ]);
    }

    // コメント一覧
    public function comments($id)
    {
        $post = Post::findOrFail($id);

        $comments = $post->comments()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'postId' => $c->post_id,
                    'userId' => $c->user_id,
                    'username' => $c->username,
                    'text' => $c->text,
                    'createdAt' => $c->created_at,
                ];
            });

        return response()->json($comments);
    }
}
