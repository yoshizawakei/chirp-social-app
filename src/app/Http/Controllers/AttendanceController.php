<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 勤怠の基本画面
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $today = Carbon::today();

        $todayAttendance = Attendance::where("user_id", $user->id)->whereDate("date", $today)->first();

        $status = "勤務外";

        if ($todayAttendance) {
            if ($todayAttendance->clock_in_time) {
                if ($todayAttendance->clock_out_time) {
                    $status = "退勤済";
                } else {
                    $status = "出勤中";

                    $latestRest = $todayAttendance->rests()->latest()->first();
                    if ($latestRest && $latestRest->start_time && !$latestRest->end_time) {
                        $status = "休憩中";
                    }
                }
            }
        }

        return view("attendance.index", compact("status", "todayAttendance"));
    }

    // 出勤処理
    public function clockIn(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $today = Carbon::today();
        $currentTime = Carbon::now();

        $attendance = Attendance::where("user_id", $user->id)->whereDate("date", $today)->first();

        if ($attendance && $attendance->clock_in_time) {
            return redirect()->route("attendance.index")->with("error", "本日既に出勤済みです。");
        }

        if (!$attendance) {
            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => $today->toDateString(),
                "clock_in_time" => $currentTime->toTimeString(),
            ]);
        } else {
            $attendance->update([
            "clock_in_time" => $currentTime->toTimeString(),
            ]);
        }

        return redirect()->route("attendance.index")->with("success", "出勤を記録しました。");
    }

    // 退勤処理
    public function clockOut(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $today = Carbon::today();
        $currentTime = Carbon::now();

        $attendance = Attendance::where("user_id", $user->id)->whereDate("date", $today)->first();

        if (!$attendance || !$attendance->clock_in_time) {
            return redirect()->route("attendance.index")->with("error", "出勤記録がありません。");
        }
        if ($attendance->clock_out_time) {
            return redirect()->route("attendance.index")->with("error", "既に退勤済みです。");
        }

        $latestRest = $attendance->rests()->latest()->first();
        if ($latestRest && $latestRest->start_time && !$latestRest->end_time) {
            return redirect()->route("attendance.index")->with("error", "休憩中のため、退勤できません。");
        }

        $attendance->update([
            "clock_out_time" => $currentTime->toTimeString(),
        ]);
        return redirect()->route("attendance.index")->with("success", "退勤を記録しました。");
    }

    // 休憩入処理
    public function breakIn(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $today = Carbon::today();
        $currentTime = Carbon::now();

        $attendance = Attendance::where("user_id", $user->id)->whereDate("date", $today)->first();

        if (!$attendance || !$attendance->clock_in_time || $attendance->clock_out_time) {
            return redirect()->route("attendance.index")->with("error", "出勤記録がありません。");
        }

        $latestRest = $attendance->rests()->latest()->first();
        if ($latestRest && $latestRest->start_time && !$latestRest->end_time) {
            return redirect()->route("attendance.index")->with("error", "既に休憩中です。");
        }

        Rest::create([
            "attendance_id" => $attendance->id,
            "start_time" => $currentTime->toTimeString(),
            "end_time" => null,
        ]);

        return redirect()->route("attendance.index")->with("success", "休憩を開始しました。");
    }

    // 休憩出処理
    public function breakOut(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $today = Carbon::today();
        $currentTime = Carbon::now();

        $attendance = Attendance::where("user_id", $user->id)->whereDate("date", $today)->first();

        if (!$attendance || !$attendance->clock_in_time || $attendance->clock_out_time) {
            return redirect()->route("attendance.index")->with("error", "出勤記録がありません。又は、既に退勤済みです。");
        }

        $latestRest = $attendance->rests()->latest()->first();

        if (!$latestRest || !$latestRest->start_time || $latestRest->end_time) {
            return redirect()->route("attendance.index")->with("error", "休憩中ではありません。");
        }

        $latestRest->update([
            "end_time" => $currentTime->toTimeString(),
        ]);

        return redirect()->route("attendance.index")->with("success", "休憩を終了しました。");
    }

    // 勤怠一覧
    public function list(Request $request, $year = null, $month = null)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $displayDate = Carbon::createFromDate($year ?? Carbon::now()->year, $month ?? Carbon::now()->month, 1);

        $attendances = Attendance::with("rests")
            ->where("user_id", $user->id)->whereMonth("date", $displayDate->month)
            ->whereYear("date", $displayDate->year)
            ->orderBy("date", "asc")->get();

        foreach ($attendances as $attendance) {
            $totalBreakTime = 0;
            foreach ($attendance->rests as $rest) {
                if ($rest->start_time && $rest->end_time) {
                    $start = Carbon::parse($rest->start_time);
                    $end = Carbon::parse($rest->end_time);
                    $totalBreakTime += $end->diffInMinutes($start);
                }
            }
            $attendance->total_break_time = sprintf("%02d:%02d", floor($totalBreakTime / 60), $totalBreakTime % 60);

            $totalWorkTime = 0;
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $totalWorkTime = $clockOut->diffInMinutes($clockIn) - $totalBreakTime;
            }
            $attendance->total_work_time = sprintf("%02d:%02d", floor($totalWorkTime / 60), $totalWorkTime % 60);

            $attendance->formatted_date = Carbon::parse($attendance->date)->isoFormat("MM/DD(ddd)");
        }

        $currentMonthYear = $displayDate->isoFormat("YYYY/MM");
        $prevMonthYear = $displayDate->copy()->subMonth();
        $nextMonthYear = $displayDate->copy()->addMonth();

        return view("attendance.list", compact("attendances", "displayDate", "currentMonthYear", "prevMonthYear", "nextMonthYear"));
    }

    // 勤怠詳細
    public function detail(Attendance $attendance)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        if ($attendance->user_id !== $user->id) {
            return redirect()->route("attendance.index")->with("error", "他のユーザーの勤怠情報は表示できません。");
        }

        $attendance->load("rests");

        $attendance->formatted_date = Carbon::parse($attendance->date)->isoFormat("YYYY年/MM月/DD日(ddd)");

        return view("attendance.detail", compact("attendance"));
    }


}
