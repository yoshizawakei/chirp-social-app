<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_registration_validation_messages()
    {
        // 名前のバリデーション
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください。']);

        // メールアドレスのバリデーション
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください。']);

        // パスワードが8文字未満のバリデーション
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください。']);

        // パスワードと確認用パスワードが一致しないバリデーション
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません。']);

        // パスワードが未入力のバリデーション
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください。']);
    }

    public function test_user_register_successfully()
    {
        $userData = [
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/email/verify');

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
        ]);
    }
}
