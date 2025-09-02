<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 200);
        }

        $user = Auth::user();

        // ユーザーがメールアドレスの確認を必要とし、まだ確認していない場合、
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // ユーザーか管理者かでリダイレクト先を変更
        if ($user->isAdmin()) {
            return redirect()->route('admin.attendance.list');
        } elseif ($user->isUser()) {
            return redirect()->route('attendance.index');
        }

        // デフォルトのリダイレクト先
        return redirect(Fortify::redirects('login'));
    }
}
