<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // コメント追加
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $data = $request->validate([
            'userId' => 'required|string',
            'username' => 'required|string|max:50',
            'text' => 'required|string|max:120',
        ]);

        $comment = $post->comments()->create([
            'user_id' => $data['userId'],
            'username' => $data['username'],
            'text' => $data['text'],
        ]);

        return response()->json($comment, 201);
    }
}
