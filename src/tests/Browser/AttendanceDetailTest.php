<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceDetailTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * 勤怠詳細画面の「名前」がログインユーザーの氏名になっていることを確認するテスト
     *
     * @return void
     */
    public function test_name_is_displayed_on_attendance_detail()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today()->addDays(1),
                "clock_in_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->assertSee($user->name);
        });
    }

    public function test_date_is_displayed_on_attendance_detail()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $testDate = Carbon::today()->addDays(2);
            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => $testDate,
                "clock_in_time" => Carbon::now(),
            ]);

            $expectedYear = $testDate->isoFormat("YYYY年");
            $expectedMonthDay = $testDate->isoFormat("M月D日");

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->assertSee($expectedYear)
                ->assertSee($expectedMonthDay);
        });
    }

    public function test_clock_in_and_out_times_are_correctly_displayed()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $clockInTime = Carbon::parse("09:00")->format("H:i");
            $clockOutTime = Carbon::parse("17:00")->format("H:i");

            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today()->addDays(3),
                "clock_in_time" => $clockInTime,
                "clock_out_time" => $clockOutTime,
            ]);

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->assertInputValue("input[name='clock_in_time_after']", $clockInTime)
                ->assertInputValue("input[name='clock_out_time_after']", $clockOutTime);
        });
    }

    /**
     * 「休憩」にて記されている時間がログインユーザーの打刻と一致していることを確認するテスト
     *
     * @return void
     */
    public function test_break_time_is_correctly_displayed()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today()->addDays(4),
                "clock_in_time" => Carbon::now()->subHours(8),
                "clock_out_time" => Carbon::now(),
            ]);

            $breakStart = Carbon::parse("12:00")->format("H:i");
            $breakEnd = Carbon::parse("12:30")->format("H:i");
            Rest::create([
                "attendance_id" => $attendance->id,
                "start_time" => $breakStart,
                "end_time" => $breakEnd,
            ]);

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->assertInputValue("input[name='rests_after[0][start]']", $breakStart)
                ->assertInputValue("input[name='rests_after[0][end]']", $breakEnd);
        });
    }
}