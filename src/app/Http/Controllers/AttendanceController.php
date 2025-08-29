<?php

namespace App\Http\Controllers;

use App\Models\CorrectionApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\CorrectionRequest;
use App\Models\User;


class AttendanceController extends Controller
{
    // ユーザーログイン画面
    public function login()
    {
        return view("auth.login");
    }
    // ユーザーログイン処理
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only("email", "password");

        if (Auth::attempt($credentials)) {
            if (!Auth::user()->hasVerifiedEmail()) {
            return redirect()->route("attendance.verifyEmail")->with("error", "メールアドレスが確認されていません。");
            }
            return redirect()->route("attendance.index");
        }

        return redirect()->route("attendance.login");
    }

    // メール認証画面
    public function verifyEmail()
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect()->route("attendance.index");
        }
        return view("auth.verify-email");
    }

    // ユーザー登録画面
    public function register()
    {
        return view("auth.register");
    }

    // ユーザー登録処理
    public function store(RegisterRequest $request)
    {
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
        ]);

        Auth::login($user);

        if ($user->email_verified_at === null) {
            $user->sendEmailVerificationNotification();
            return redirect()->route("attendance.verifyEmail");
        }

        return redirect()->route("attendance.index")->with("success", "ユーザー登録が完了しました。");
    }

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
            ->where("user_id", $user->id)
            ->whereMonth("date", $displayDate->month)
            ->whereYear("date", $displayDate->year)
            ->orderBy("date", "asc")->get();

        $formattedAttendances = [];
        foreach ($attendances as $attendance) {
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
                "formatted_date" => Carbon::parse($attendance->date)->isoFormat("MM/DD(ddd)"),
                "formatted_clock_in_time" => $attendance->clock_in_time ? Carbon::parse($attendance->clock_in_time)->format("H:i") : "-",
                "formatted_clock_out_time" => $attendance->clock_out_time ? Carbon::parse($attendance->clock_out_time)->format("H:i") : "-",
                "total_break_time" => $formattedTotalBreakTime,
                "total_work_time" => $formattedTotalWorkTime,
            ];
        }

        $currentMonthYear = $displayDate->isoFormat("YYYY/MM");
        $prevMonthYear = $displayDate->copy()->subMonth();
        $nextMonthYear = $displayDate->copy()->addMonth();

        return view("attendance.list", compact(
            "formattedAttendances",
            "displayDate",
            "currentMonthYear",
            "prevMonthYear",
            "nextMonthYear",
        ));
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

        $formattedDateYear = Carbon::parse((string) $attendance->date)->isoFormat("YYYY年");
        $formattedDateMonthDay = Carbon::parse((string) $attendance->date)->isoFormat("M月D日");

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
        return "不明なステータス";
    }

    // 勤怠修正申請
    public function requestModify(CorrectionRequest $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        $attendance = Attendance::findOrFail($id);

        if ($attendance->user_id !== $user->id) {
            return redirect()->route("attendance.index")->with("error", "他のユーザーの勤怠情報は修正できません。");
        }

        $attendance = Attendance::with("rests")->find($id);

        if (!$attendance) {
            return redirect()->route("attendance.index")->with("error", "指定された勤怠情報が見つかりません。");
        }

        $restsBefore = [];
        foreach ($attendance->rests as $rest) {
            $restsBefore[] = [
                "start" => $rest->start_time ? Carbon::parse($rest->start_time)->format("H:i") : null,
                "end" => $rest->end_time ? Carbon::parse($rest->end_time)->format("H:i") : null,
            ];
        }

        $restsAfter = [];
        if ($request->has("rests_after") && is_array($request->rests_after)) {
            foreach ($request->rests_after as $index => $restData) {
                $start = $restData["start"] ?? null;
                $end = $restData["end"] ?? null;

                if ($start !== null || $end !== null) {
                    $restsAfter[] = [
                        "start" => $start,
                        "end" => $end,
                    ];
                }
            }
        }

        CorrectionApplication::create([
            "user_id" => $user->id,
            "attendance_id" => $attendance->id,
            "clock_in_time_before" => $attendance->clock_in_time,
            "clock_out_time_before" => $attendance->clock_out_time,
            "rests_before" => json_encode($restsBefore),
            "notes_before" => $attendance->notes,
            "clock_in_time_after" => $request->clock_in_time_after,
            "clock_out_time_after" => $request->clock_out_time_after,
            "rests_after" => json_encode($restsAfter),
            "notes_after" => $request->notes_after,
            "is_approved" => false,
        ]);

        return redirect()->route("attendance.list", ["attendance" => $attendance])->with("success", "勤怠修正申請を送信しました。");
    }


}
