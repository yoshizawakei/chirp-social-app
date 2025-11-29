<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // パスワードは 'password'
        ]);
        User::factory()->create([
            'name' => 'testuser2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'), // パスワードは 'password'
        ]);
    }
}
