<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;


class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Attendance::class;

    public function definition()
    {
        // 過去の日付をランダムに生成
        $date = $this->faker->unique()->dateTimeBetween('-30 days', 'yesterday')->format('Y-m-d');

        // 出勤時間と退勤時間をランダムに生成
        $clockInTime = Carbon::parse($date . ' 09:00:00')->addMinutes(rand(-30, 30));
        $clockOutTime = Carbon::parse($date . ' 17:00:00')->addMinutes(rand(-30, 30));

        // 勤怠データを作成
        return [
            "user_id" => \App\Models\User::factory(),
            "date" => $date,
            "clock_in_time" => $clockInTime->toTimeString(),
            "clock_out_time" => $clockOutTime->toTimeString(),
            "notes" => null,
        ];
    }

    /**
     * 特定のユーザーに関連付けるための状態
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUser(int $userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }

    /**
     * 休憩中、または勤務外の状態を生成するための状態
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withoutClockOut()
    {
        return $this->state(function (array $attributes) {
            return [
                'clock_out_time' => null,
            ];
        });
    }
}