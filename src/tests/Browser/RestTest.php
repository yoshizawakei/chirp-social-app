<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Rest;

class RestTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_rest_in_button_works()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::now(),
                "clock_in_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance")
                ->refresh()
                ->assertSee("休憩入")
                ->press("休憩入")
                ->waitForText("休憩中", 5)
                ->assertSee("休憩中");
        });
    }

    public function test_can_take_multiple_rest()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::now(),
                "clock_in_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance")
                ->refresh()
                ->assertSee("休憩入")
                ->press("休憩入")
                ->waitForText("休憩中", 5)
                ->refresh()
                ->assertSee("休憩戻")
                ->press("休憩戻")
                ->waitForText("出勤中", 5)
                ->assertSee("出勤中");

            $browser->assertSee("休憩入")
                ->assertDontSee("休憩戻");
        });
    }

    public function test_rest_out_button_works()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::now(),
                "clock_in_time" => Carbon::now()->subMinutes(30),
            ]);
            Rest::create([
                "attendance_id" => $attendance->id,
                "start_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance")
                ->refresh()
                ->assertSee("休憩戻")
                ->press("休憩戻")
                ->waitForText("出勤中", 5)
                ->assertSee("出勤中");
        });
    }

    public function test_can_rest_multiple_times()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::now(),
                "clock_in_time" => Carbon::now(),
            ]);
            Rest::create([
                "attendance_id" => $attendance->id,
                "start_time" => Carbon::now()->subMinutes(30),
                "end_time" => Carbon::now()->subMinutes(10),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance")
                ->refresh()
                ->assertSee("休憩入");
        });
    }

    public function test_rest_is_recorded_in_attendance()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today(),
                "clock_in_time" => Carbon::now()->subHours(8),
                "clock_out_time" => Carbon::now(),
            ]);

            Rest::create([
                "attendance_id" => $attendance->id,
                "start_time" => Carbon::now()->subMinutes(60),
                "end_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance/list")
                ->waitForText("休憩", 5)
                ->assertSee("休憩")
                ->assertSee("1:00");
        });
    }
}