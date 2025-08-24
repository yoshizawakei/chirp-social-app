<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_all_attendance_records_are_displayed()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today(),
                "clock_in_time" => Carbon::now()->subHours(8),
                "clock_out_time" => Carbon::now(),
            ]);
            Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today()->addDay(),
                "clock_in_time" => Carbon::now()->addDay()->subHours(8),
                "clock_out_time" => Carbon::now()->addDay(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance/list")
                ->assertSee(Carbon::today()->isoFormat("MM/DD"))
                ->assertSee(Carbon::today()->addDay()->isoFormat("MM/DD"));
        });
    }

    public function test_current_month_displayed_on_my_list()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $expectedMonthYear = Carbon::now()->isoFormat("YYYY/MM");

            $browser->loginAs($user)
                ->visit("/attendance/list")
                ->assertSeeIn('.current-month', $expectedMonthYear);
        });
    }

    public function test_prev_month_button_displays_prev_month()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $prevMonthYear = Carbon::now()->subMonth()->isoFormat("YYYY/MM");

            $browser->loginAs($user)
                ->visit("/attendance/list")
                ->clickLink("前月")
                ->pause(1000)
                ->waitForTextIn('.current-month', $prevMonthYear, 5)
                ->assertSeeIn('.current-month', $prevMonthYear);
        });
    }

    public function test_next_month_button_displays_next_month()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $nextMonthYear = Carbon::now()->addMonth()->isoFormat("YYYY/MM");

            $browser->loginAs($user)
                ->visit("/attendance/list")
                ->clickLink("翌月")
                ->pause(1000)
                ->waitForTextIn('.current-month', $nextMonthYear, 5)
                ->assertSeeIn('.current-month', $nextMonthYear);
        });
    }

    public function test_detail_link_navigates_to_detail_page()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $attendance = Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::today(),
                "clock_in_time" => Carbon::now()->subHours(8),
                "clock_out_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance/list")
                ->assertSee("詳細")
                ->clickLink("詳細")
                ->assertSee("勤怠詳細");
        });
    }
}