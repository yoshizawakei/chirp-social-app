<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rest;
use App\Models\Attendance;
use Carbon\Carbon;

class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Rest::class;

    public function definition()
    {
        // ランダムな休憩時間を生成
        $startTime = Carbon::createFromTime($this->faker->numberBetween(12, 13), $this->faker->numberBetween(0, 59), 0);
        $endTime = $startTime->copy()->addMinutes($this->faker->numberBetween(30, 60));

        return [
            "attendance_id" => Attendance::factory(), // Attendanceモデルと関連付け
            "start_time" => $startTime->toTimeString(),
            "end_time" => $endTime->toTimeString(),
        ];
    }

    /**
     * 特定の勤怠記録に関連付けるための状態
     * @param int $attendanceId
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forAttendance(int $attendanceId)
    {
        return $this->state(function (array $attributes) use ($attendanceId) {
            return [
                'attendance_id' => $attendanceId,
            ];
        });
    }

    /**
     * 休憩中の状態を生成するための状態（終了時間がない）
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withoutEndTime()
    {
        return $this->state(function (array $attributes) {
            return [
                'end_time' => null,
            ];
        });
    }
}