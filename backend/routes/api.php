<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

Route::prefix('posts')->group(function () {

    // 投稿
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::get('{id}', [PostController::class, 'show']);
    Route::delete('{id}', [PostController::class, 'destroy']);

    // コメント
    Route::get('{id}/comments', [PostController::class, 'comments']);
    Route::post('{id}/comments', [CommentController::class, 'store']);

    // ★コメント編集・削除（ここが今回の正しい部分）
    Route::put('{id}/comments/{commentId}', [CommentController::class, 'update']);
    Route::delete('{id}/comments/{commentId}', [CommentController::class, 'destroy']);

    // いいね
    Route::post('{id}/like', [PostController::class, 'toggleLike']);

});
