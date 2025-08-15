<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存の全ユーザーを取得
        $users = User::all();

        foreach ($users as $user) {
            // 過去30日間の勤怠データを生成
            $days = 30;
            for ($i = $days; $i >= 1; $i--) {
                $date = Carbon::today()->subDays($i);

                // 出勤時間と退勤時間を作成
                $clockIn = $date->copy()->setTime(9, rand(0, 59));
                $clockOut = $date->copy()->setTime(17, rand(0, 59));

                // Attendance レコードを作成
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'clock_in_time' => $clockIn->toTimeString(),
                    'clock_out_time' => $clockOut->toTimeString(),
                ]);

                // 休憩データを作成（1回または2回）
                $numberOfRests = rand(1, 2);
                for ($j = 0; $j < $numberOfRests; $j++) {
                    $restStart = $clockIn->copy()->addMinutes(rand(60, 180));
                    $restEnd = $restStart->copy()->addMinutes(rand(30, 60));

                    Rest::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => $restStart->toTimeString(),
                        'end_time' => $restEnd->toTimeString(),
                    ]);
                }
            }
        }
    }
}