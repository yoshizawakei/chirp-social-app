<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Notification;
use Tests\DuskTestCase;
use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class MailAuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    // 会員登録後、認証メールが送信されるテストはunittestで実施済み

    /**
     * メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     *
     * @return void
     * @throws Throwable
     */
    public function test_email_verification_leads_to_page()
    {
        $this->browse(function (Browser $browser) {
            // データベースに未認証のユーザーを作成
            $user = User::create([
                "name" => "Test User",
                "email" => "test_user@example.com",
                "password" => bcrypt("password"),
                "email_verified_at" => Carbon::now(),
            ]);

            // 認証リンクにアクセスし、メール認証画面に遷移することを確認
            $browser->loginAs($user)
                ->visit(route("attendance.verifyEmail"))
                ->waitForText('認証はこちらから')
                ->clickLink('認証はこちらから')
                ->assertUrlIs("https://mailtrap.io/signin");

        });
    }

    /**
     * メール認証サイトのメール認証を完了すると、勤怠登録画面に遷移するテスト
     *
     * @return void
     * @throws Throwable
     */
    public function test_email_verification_completion_redirects_to_attendance_page()
    {
        $this->browse(function (Browser $browser) {
            // データベースに未認証のユーザーを作成
            $user = User::create([
                "name" => "Test User",
                "email" => "test_user@example.com",
                "password" => bcrypt("password"),
                "email_verified_at" => null,
            ]);

            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->id,
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            $browser->loginAs($user)
                ->visit($verificationUrl)
                ->assertPathIs(route('attendance.index', [], false))
                ->assertSee('勤務外');

            $this->assertNotNull($user->fresh()->email_verified_at);
        });
    }
}