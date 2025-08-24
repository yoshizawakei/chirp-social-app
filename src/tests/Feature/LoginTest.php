<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\UsersTableSeeder;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);

    }

    public function test_login_validation()
    {
        $response = $this->post("/login", [
            "email" => "",
            "password" => "password",
        ]);
        $response->assertSessionHasErrors(["email" => "メールアドレスを入力してください。"]);

        $response = $this->post("/login", [
            "email" => "test@example.com",
            "password" => "",
        ]);
        $response->assertSessionHasErrors(["password" => "パスワードを入力してください。"]);
    }

    public function test_user_login_fails()
    {
        $response = $this->post("/login", [
            "email" => "wrong@example.com",
            "password" => "password",
        ]);
        $response->assertSessionHasErrors(["email" => "ログイン情報が登録されていません。"]);
    }

    public function test_user_can_login()
    {
        $response = $this->post("/login", [
            "email" => "test@example.com",
            "password" => "password",
        ]);

        $response->assertRedirect("/email/verify");

        $this->assertAuthenticatedAs(User::where("email", "test@example.com")->first());
    }
}