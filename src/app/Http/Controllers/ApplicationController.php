<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionApplication;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login")->with("error", "ログインしてください。");
        }

        $query = CorrectionApplication::with(["user", "attendance"]);

        $status = $request->query("status", "pending");

        if ($status === "pending") {
            $query->where(function($q) {
                $q->whereNull("is_approved")->orWhere("is_approved", false);
            });
        } elseif ($status === "approved") {
            $query->where("is_approved", true);
        }

        $applications = $query->orderBy("created_at", "desc")->get();

        return view("application.list", compact("applications", "status"));
    }

    public function detail(CorrectionApplication $application)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route("login");
        }

        if ($application->user_id !== $user->id) {
            return redirect()->route("attendance.index")->with("error", "他のユーザーの勤怠情報は表示できません。");
        }

        $application->load("user", "attendance");

        $formattedTargetDate = Carbon::parse($application->attendance->date)->isoFormat("YYYY年M月D日");
        $formattedApplyDate = Carbon::parse($application->created_at)->isoFormat("YYYY年M月D日 H:i");

        $formattedClockInBefore = $application->clock_in_time_before ? Carbon::parse($application->clock_in_time_before)->format("H:i") : "-";
        $formattedClockOutBefore = $application->clock_out_time_before ? Carbon::parse($application->clock_out_time_before)->format("H:i") : "-";
        $formattedClockInAfter = $application->clock_in_time_after ? Carbon::parse($application->clock_in_time_after)->format("H:i") : "-";
        $formattedClockOutAfter = $application->clock_out_time_after ? Carbon::parse($application->clock_out_time_after)->format("H:i") : "-";

        $restsBefore = json_decode($application->rests_before, true);
        $restsAfter = json_decode($application->rests_after, true);

        $formattedRestsBefore = [];
        if (is_array($restsBefore)) {
            foreach ($restsBefore as $rest) {
                $formattedRestsBefore[] = [
                    "start" => $rest["start"] ?? "-",
                    "end" => $rest["end"] ?? "-"
                ];
            }
        }

        $formattedRestsAfter = [];
        if (is_array($restsAfter)) {
            foreach ($restsAfter as $rest) {
                $formattedRestsAfter[] = [
                    "start" => $rest["start"] ?? "-",
                    "end" => $rest["end"] ?? "-"
                ];
            }
        }

        $formattedNotesBefore = $application->notes_before ?? "-";
        $formattedNotesAfter = $application->notes_after ?? "-";

        return view("application.detail", compact(
            "application",
            "formattedTargetDate",
            "formattedApplyDate",
            "formattedClockInBefore",
            "formattedClockOutBefore",
            "formattedClockInAfter",
            "formattedClockOutAfter",
            "formattedRestsBefore",
            "formattedRestsAfter",
            "formattedNotesBefore",
            "formattedNotesAfter"
        ));
    }
}
