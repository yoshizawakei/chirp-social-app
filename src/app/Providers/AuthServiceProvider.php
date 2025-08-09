<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\MyVerifyEmailNotification;
use Laravel\Fortify\Fortify;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Responses\CustomLoginResponse;
use App\Http\Responses\CustomRegisterResponse;
use App\Providers\RouteServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Fortify のカスタムレスポンスを設定
        Gate::define("isAdmin", function ($user) {
            return $user->is_admin;
        });

        // VerifyEmail の通知をカスタマイズ
        VerifyEmail::toMailUsing(function ($notifiable, $verificationUrl) {
            return (new MyVerifyEmailNotification($verificationUrl))->toMail($notifiable);
        });
    }
}
