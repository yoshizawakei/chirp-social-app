<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // コメント追加
    public function store(Request $request, $postId)
    {
        $data = $request->validate([
            'userId' => 'required|string',
            'username' => 'required|string|max:50',
            'text' => 'required|string|max:120',  // コメント 120文字以内
        ]);

        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $data['userId'],
            'username' => $data['username'],
            'text' => $data['text'],
        ]);

        return response()->json($comment, 201);
    }


    // コメント編集
    public function update(Request $request, $postId, $commentId)
    {
        $data = $request->validate([
            'userId' => 'required|string',
            'text' => 'required|string|max:120',
        ]);

        $comment = Comment::findOrFail($commentId);

        // 自分のコメントのみ編集可能
        if ($comment->user_id !== $data['userId']) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment->update([
            'text' => $data['text'],
        ]);

        return response()->json($comment);
    }


    // コメント削除
    public function destroy(Request $request, $postId, $commentId)
    {
        $data = $request->validate([
            'userId' => 'required|string',
        ]);

        $comment = Comment::findOrFail($commentId);

        // 自分のコメントのみ削除可能
        if ($comment->user_id !== $data['userId']) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'deleted']);
    }
}
