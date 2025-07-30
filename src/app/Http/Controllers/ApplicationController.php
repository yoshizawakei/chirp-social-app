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

        $query = CorrectionApplication::with(["user", "attendance"])
            ->where("user_id", $user->id);

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

}
