<?php

use App\Http\Controllers\AdminController;
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

// ユーザーログイン画面
Route::get("/", [AttendanceController::class, "login"])->name("attendance.login");
// ユーザーログイン処理
Route::post("/login", [AttendanceController::class, "authenticate"])->name("attendance.authenticate");
// ユーザー登録画面
Route::get("/register", [AttendanceController::class, "register"])->name("attendance.register");
// ユーザー登録処理
Route::post("/register", [AttendanceController::class, "store"])->name("attendance.store");

// ユーザー関係
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

    // 申請一覧
    Route::get("/stamp_correction_request/list", [ApplicationController::class, "list"])->name("application.list");

    // 申請詳細
    Route::get("/application/detail/{application}", [ApplicationController::class, "detail"])->name("application.detail");
});


// 管理者関係
// 管理者ログイン
Route::get("/admin/login", [AdminController::class, "login"])->name("admin.login");
// 管理者ログイン処理
Route::post("/admin/login", [AdminController::class, "authenticate"])->name("admin.authenticate");
// 管理者勤怠一覧
Route::get("/admin/attendance/list/{dateString?}", [AdminController::class, "attendanceList"])->name("admin.attendance.list");
// 管理者勤怠詳細
Route::get("/admin/attendance/{attendance}", [AdminController::class, "adminDetail"])->name("admin.attendance.detail");
// 管理者勤怠修正
Route::post("/admin/attendance/modify/{id}", [AdminController::class, "modifyAttendance"])->name("admin.attendance.modify");
// スタッフ一覧
Route::get("/admin/staff/list", [AdminController::class, "staffList"])->name("admin.staff.list");
// スタッフ別勤怠一覧
Route::get("/admin/attendance/staff/{id}/{year?}/{month?}", [AdminController::class, "staffDetail"])->name("admin.staff.detail");
// スタッフ別勤怠一覧CSV出力
Route::post("/admin/staff/{id}/{year?}/{month?}/csv", [AdminController::class, "exportCsv"])->name("admin.staff.exportCsv");
// 管理者修正申請一覧
Route::get("/admin/stamp_correction_request/list", [AdminController::class, "correctionRequestList"])->name("admin.correctionRequest.list");
// 管理者修正申請詳細
Route::get("/admin/stamp_correction_request/detail/{attendance}", [AdminController::class, "correctionRequestDetail"])->name("admin.correctionRequest.detail");
// 管理者承認処理
Route::post("/admin/stamp_correction_request/approve/{id}", [AdminController::class, "approveCorrection"])->name("admin.correctionRequest.approve");


// ログアウト
Route::post("/logout", function () {
    Auth::logout();
    return redirect()->route("attendance.index");
})->name("logout");
Route::post("/admin/logout", function () {
    Auth::logout();
    return redirect()->route("admin.login");
})->name("admin.logout");