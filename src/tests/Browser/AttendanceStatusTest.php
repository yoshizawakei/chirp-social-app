<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Rest;

class AttendanceStatusTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function test_attendance_status_outside_work()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $browser->loginAs($user)
                    ->visit("/attendance")
                    ->waitForText("勤務外", 5)
                    ->assertSee("勤務外");
        });
    }

    public function test_attendance_status_is_clocked_in()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'clock_in_time' => Carbon::now(),
            ]);

            $browser->loginAs($user)
                    ->visit("/attendance")
                    ->waitForText("出勤中", 5)
                    ->assertSee("出勤中");
        });
    }

    public function test_attendance_status_is_on_break()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'clock_in_time' => Carbon::now()->subMinutes(30),
            ]);

            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);

            $browser->loginAs($user)
                    ->visit('/attendance')
                    ->refresh()
                    ->waitForText('休憩中', 5)
                    ->assertSee('休憩中');
        });
    }
    public function test_attendance_status_is_clocked_out()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            Attendance::create([
                'user_id' => $user->id,
                'date' => Carbon::now(),
                'clock_in_time' => Carbon::now()->subHours(8),
                'clock_out_time' => Carbon::now(),
            ]);

            $browser->loginAs($user)
                    ->visit('/attendance')
                    ->refresh()
                    ->waitForText('退勤済', 5)
                    ->assertSee('退勤済');
        });
    }
}
