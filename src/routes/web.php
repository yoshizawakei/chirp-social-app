<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApplicationController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(["auth"])->group(function () {
    // 勤怠関係
    Route::get("/attendance", [AttendanceController::class, "index"])->name("attendance.index");
    Route::post("/attendance/clock-in", [AttendanceController::class, "clockIn"])->name("attendance.clockIn");
    Route::post("/attendance/clock-out", [AttendanceController::class, "clockOut"])->name("attendance.clockOut");
    Route::post("/attendance/break-in", [AttendanceController::class, "breakIn"])->name("attendance.breakIn");
    Route::post("/attendance/break-out", [AttendanceController::class, "breakOut"])->name("attendance.breakOut");

    // 勤怠一覧
    Route::get("/attendance/list/{year?}/{month?}", [AttendanceController::class, "list"])->name("attendance.list");

    // 勤怠詳細
    Route::get("/attendance/{attendance}", [AttendanceController::class, "detail"])->name("attendance.detail");

    // 勤怠修正申請
    Route::post("/attendance/request-modify/{id}", [AttendanceController::class, "requestModify"])->name("attendance.requestModify");

    // 勤怠修正申請一覧
    Route::get("/application/list", [ApplicationController::class, "list"])->name("application.list");

    // 勤怠修正申請詳細
    Route::get("/application/detail/{application}", [ApplicationController::class, "detail"])->name("application.detail");
});

// ログアウト
Route::post("/logout", function () {
    Auth::logout();
    return redirect()->route("attendance.index");
})->name("logout");