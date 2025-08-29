<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 会員登録後に認証メールが送信される
     * @return void
     */
    public function test_email_is_sent_after_registration()
    {
        Notification::fake();

        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 1, // 一般ユーザー
        ];

        $response = $this->post('/register', $userData);

        $user = User::where('email', $userData['email'])->first();

        // 登録後のリダイレクト先を検証
        $response->assertRedirect(route('attendance.verifyEmail'));

        // 認証メールが送信されたことを確認
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}