<?php

namespace Tests\Browser;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;

class ClockOutTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */
    
    public function test_clock_out_button_works()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            Attendance::create([
                "user_id" => $user->id,
                "date" => now(),
                "clock_in_time" => now()->subHours(4),
            ]);

            $browser->loginAs($user)
                ->visit("/attendance")
                ->refresh()
                ->assertSee("退勤")
                ->press("退勤")
                ->waitForText("退勤済", 5)
                ->assertSee("退勤済");
        });
    }

    public function test_clock_out_time_recorded()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();

            $browser->loginAs($user)
                ->visit("/attendance")
                ->refresh()
                ->assertSee("出勤")
                ->press("出勤")
                ->waitForText("出勤中", 5)
                ->assertSee("退勤")
                ->press("退勤")
                ->waitForText("退勤済", 5);

            $expectedTime = Carbon::now()->isoFormat('HH:mm');

            $browser->visit("/attendance/list")
                ->assertSee( $expectedTime);
        });
    }
}
