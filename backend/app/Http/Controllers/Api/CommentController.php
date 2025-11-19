<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;


class CommentController extends Controller
{
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
}
