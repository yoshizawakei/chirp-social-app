<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            [
                "name" => "山田太郎",
                "email" => "test@example.com",
                "email_verified_at" => null,
                "password" => bcrypt("password"),
                "role" => 1, // 一般ユーザー
                "remember_token" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
        DB::table("users")->insert([
            [
                "name" => "山田花子",
                "email" => "test@example.co.nz",
                "email_verified_at" => null,
                "password" => bcrypt("password"),
                "role" => 1, // 一般ユーザー
                "remember_token" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
        DB::table("users")->insert([
            [
                "name" => "佐藤花子",
                "email" => "test@example.co.jp",
                "email_verified_at" => null,
                "password" => bcrypt("password"),
                "role" => 2, // 管理者
                "remember_token" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
    }
}
