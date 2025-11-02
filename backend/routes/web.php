<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\TweetController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('tweets')->group(function () {
    Route::get('/', [TweetController::class, 'index']);
    Route::post('/', [TweetController::class, 'store']);
    Route::delete('/{tweet}', [TweetController::class, 'destroy']);
});

Route::prefix('tweets/{tweet}/comments')->group(function () {
    Route::get('/', [CommentController::class, 'index']);
    Route::post('/', [CommentController::class, 'store']);
});

// コメント削除は、コメントIDで直接指定する場合
// Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

Route::prefix('tweets/{tweet}')->group(function () {
    Route::post('/like', [LikeController::class, 'store']);
    Route::delete('/like', [LikeController::class, 'destroy']);
});