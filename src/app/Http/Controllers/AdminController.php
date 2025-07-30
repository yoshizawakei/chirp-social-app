<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\CorrectionApplication;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function login()
    {
        return view("admin.login");
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only("email", "password");

        if (auth()->attempt($credentials)) {
            if (!auth()->user()->isAdmin()) {
                auth()->logout();
                return redirect()->back()->with("error", "管理者アカウントでログインしてください。");
            }
            // ログイン成功
            return redirect()->route("admin.attendance.list")->with("success", "ログインしました。");
        }

        // ログイン失敗
        return redirect()->back()->with("error", "メールアドレスまたはパスワードが間違っています。");
    }

    public function attendanceList(Request $request, $dateString = null)
    {
        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            return redirect()->route("login")->with("error", "管理者アカウントでログインしてください。");
        }

        try {
            $displayDate = $dateString ? Carbon::parse($dateString) : Carbon::today();
        } catch (\Exception $e) {
            $displayDate = Carbon::today();
        }

        $rawAttendances = Attendance::with(["user", "rests"])->whereDate("date", $displayDate->toDateString())->get();

        $sortedAttendances = $rawAttendances->sortBy(function ($attendance) {
            return $attendance->user ? $attendance->user->name : '';
        });

        $formattedAttendances = [];

        foreach ($sortedAttendances as $attendance) {
            $totalBreakTime = 0;
            foreach ($attendance->rests as $rest) {
                if ($rest->start_time && $rest->end_time) {
                    $start = Carbon::parse($rest->start_time);
                    $end = Carbon::parse($rest->end_time);
                    $totalBreakTime += $end->diffInMinutes($start);
                }
            }
            $formattedTotalBreakTime = sprintf("%02d:%02d", floor($totalBreakTime / 60), $totalBreakTime % 60);

            $totalWorkTime = 0;
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $totalWorkTime = $clockOut->diffInMinutes($clockIn) - $totalBreakTime;
            }

            $formattedTotalWorkTime = sprintf("%02d:%02d", floor($totalWorkTime / 60), $totalWorkTime % 60);

            $formattedAttendances[] = (object) [
                "id" => $attendance->id,
                "user_name" => $attendance->user ? $attendance->user->name : '不明なユーザー',
                "formatted_date" => Carbon::parse($attendance->date)->isoFormat("YYYY/MM/DD(ddd)"),
                "formatted_clock_in_time" => $attendance->clock_in_time ? Carbon::parse($attendance->clock_in_time)->format("H:i") : "-",
                "formatted_clock_out_time" => $attendance->clock_out_time ? Carbon::parse($attendance->clock_out_time)->format("H:i") : "-",
                "total_break_time" => $formattedTotalBreakTime,
                "total_work_time" => $formattedTotalWorkTime,
            ];
        }

        $prevDay = $displayDate->copy()->subDay()->toDateString();
        $nextDay = $displayDate->copy()->addDay()->toDateString();

        return view("admin.attendance-list", compact(
            "formattedAttendances",
            "displayDate",
            "prevDay",
            "nextDay",
        ));
    }

    // 勤怠詳細
    public function adminDetail(Attendance $attendance)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        if (!$user->isAdmin()) {
            return redirect()->route("login")->with("error", "管理者アカウントでログインしてください。");
        }

        $attendance->load("rests");

        $formattedDateYear = Carbon::parse($attendance->date)->isoFormat("YYYY年");
        $formattedDateMonthDay = Carbon::parse($attendance->date)->isoFormat("M月D日");

        $formattedClockInTime = $attendance->clock_in_time ? Carbon::parse($attendance->clock_in_time)->format("H:i") : "";
        $formattedClockOutTime = $attendance->clock_out_time ? Carbon::parse($attendance->clock_out_time)->format("H:i") : "";

        $rest1 = $attendance->rests->isNotEmpty() ? $attendance->rests->first() : null;
        $formattedRest1Start = $rest1 && $rest1->start_time ? Carbon::parse($rest1->start_time)->format("H:i") : "";
        $formattedRest1End = $rest1 && $rest1->end_time ? Carbon::parse($rest1->end_time)->format("H:i") : "";

        $rest2 = $attendance->rests->count() > 1 ? $attendance->rests->get(1) : null;
        $formattedRest2Start = $rest2 && $rest2->start_time ? Carbon::parse($rest2->start_time)->format("H:i") : "";
        $formattedRest2End = $rest2 && $rest2->end_time ? Carbon::parse($rest2->end_time)->format("H:i") : "";

        $formattedNotes = $attendance->notes ?? "";

        $correctionApplication = CorrectionApplication::where("attendance_id", $attendance->id)->latest()->first();

        $applicationStatus = null;

        if ($correctionApplication) {
            $applicationStatus = $correctionApplication->is_approved;

            $formattedClockInTime = $correctionApplication->clock_in_time_after ? Carbon::parse($correctionApplication->clock_in_time_after)->format("H:i") : $formattedClockInTime;
            $formattedClockOutTime = $correctionApplication->clock_out_time_after ? Carbon::parse($correctionApplication->clock_out_time_after)->format("H:i") : $formattedClockOutTime;

            $restsAfter = json_decode($correctionApplication->rests_after, true);
            if (is_array($restsAfter)) {
                if (isset($restsAfter[0])) {
                    $rest1After = $restsAfter[0];
                    $formattedRest1Start = $rest1After["start"] ? Carbon::parse($rest1After["start"])->format("H:i") : $formattedRest1Start;
                    $formattedRest1End = $rest1After["end"] ? Carbon::parse($rest1After["end"])->format("H:i") : $formattedRest1End;
                }
                if (isset($restsAfter[1])) {
                    $rest2After = $restsAfter[1];
                    $formattedRest2Start = $rest2After["start"] ? Carbon::parse($rest2After["start"])->format("H:i") : $formattedRest2Start;
                    $formattedRest2End = $rest2After["end"] ? Carbon::parse($rest2After["end"])->format("H:i") : $formattedRest2End;
                }
            }
            $formattedNotes = $correctionApplication->notes_after ?? $formattedNotes;
        }

        $formattedApplicationStatusText = $this->getApprovalStatusText($applicationStatus);

        $isEditable = !($applicationStatus === 0);

        return view("attendance.detail", compact(
            "attendance",
            "formattedDateYear",
            "formattedDateMonthDay",
            "formattedClockInTime",
            "formattedClockOutTime",
            "formattedRest1Start",
            "formattedRest1End",
            "formattedRest2Start",
            "formattedRest2End",
            "formattedNotes",
            "formattedApplicationStatusText",
            "correctionApplication",
            "applicationStatus",
            "isEditable"
        ));
    }

    /**
     * 承認ステータスのテキストを取得
     * @param int|null $isApproved
     * @return string
     */
    private function getApprovalStatusText($isApproved)
    {
        if ($isApproved === 0) {
            return "承認待ち";
        } elseif ($isApproved === 1) {
            return "承認済み";
        }
    }



}
