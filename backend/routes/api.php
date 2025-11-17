<!-- backend/routes/api.php -->

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);// 投稿一覧
    Route::post('/', [PostController::class, 'store']); // 新規投稿
    Route::get('{id}', [PostController::class, 'show']);// 投稿詳細
    Route::delete('{id}', [PostController::class, 'destroy']);// 投稿削除

    Route::get('{id}/comments', [PostController::class, 'comments']);// コメント一覧
    Route::post('{id}/comments', [CommentController::class, 'store']); // コメント追加

    Route::post('{id}/like', [PostController::class, 'toggleLike']);// いいねトグル
});
