<?php

namespace Tests\Browser;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;

class ClockInTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_clock_in_button_works()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $browser->loginAs($user)
                ->visit("/attendance")
                ->assertSee("出勤")
                ->press("出勤")
                ->waitForText("出勤中", 5)
                ->assertSee("出勤中");
        });
    }

    public function test_clock_in_is_once_a_day()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            Attendance::create([
                "user_id" => $user->id,
                "date" => Carbon::now(),
                "clock_in_time" => Carbon::now()->subHours(8),
                "clock_out_time" => Carbon::now(),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance")
                ->assertDontSee("出勤");
        });
    }

    public function test_clock_in_time_is_recorded()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $browser->loginAs($user)
                ->visit("/attendance")
                ->assertSee("出勤")
                ->press("出勤");

            $expectedTime = Carbon::now()->isoFormat("HH:mm");
            $browser->visit("/attendance/list")->assertSee($expectedTime);
        });
    }

}
