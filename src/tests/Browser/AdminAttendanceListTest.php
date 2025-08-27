<?php

namespace Tests\Browser;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminAttendanceListTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @var User
     */
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            "name" => "Admin User",
            "email" => "admin@example.com",
            "password" => bcrypt("password"),
            "role" => 2, // 管理者
        ]);
    }

    /**
     * その日になされた全ユーザーの勤怠情報が正確に確認できる
     *
     * @return void
     */
    public function test_all_users_attendance_can_be_confirmed_for_the_day()
    {
        $user1 = User::create(["name" => "User One", "email" => "user1@example.com", "password" => bcrypt('password')]);
        $user2 = User::create(["name" => "User Two", "email" => "user2@example.com", "password" => bcrypt("password")]);
        $today = Carbon::today();

        Attendance::create([
            "user_id" => $user1->id,
            "date" => $today,
            "clock_in_time" => Carbon::parse("09:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("18:00")->format("H:i"),
        ]);

        Attendance::create([
            "user_id" => $user2->id,
            "date" => $today,
            "clock_in_time" => Carbon::parse("10:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("19:00")->format("H:i"),
        ]);

        $this->browse(function (Browser $browser) use ($user1, $user2) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/list")
                ->assertSee("勤怠一覧")
                ->assertSee($user1->name)
                ->assertSee("09:00")
                ->assertSee("18:00")
                ->assertSee($user2->name)
                ->assertSee("10:00")
                ->assertSee("19:00");
        });
    }

    /**
     * 遷移した際に現在の日付が表示される
     *
     * @return void
     */
    public function test_current_date_is_displayed_on_access()
    {
        $today = Carbon::today()->isoFormat("YYYY/MM/DD");

        $this->browse(function (Browser $browser) use ($today) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/list")
                ->assertSee($today);
        });
    }

    /**
     * 「前日」を押下した時に前の日の勤怠情報が表示される
     *
     * @return void
     */
    public function test_previous_day_attendance_is_displayed_by_clicking_previous_button()
    {
        $yesterday = Carbon::yesterday()->isoFormat("YYYY/MM/DD");

        $this->browse(function (Browser $browser) use ($yesterday) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/list")
                ->clickLink("前日")
                ->assertSee($yesterday);
        });
    }

    /**
     * 「翌日」を押下した時に次の日の勤怠情報が表示される
     *
     * @return void
     */
    public function test_next_day_attendance_is_displayed_by_clicking_next_button()
    {
        $tomorrow = Carbon::tomorrow()->isoFormat("YYYY/MM/DD");

        $this->browse(function (Browser $browser) use ($tomorrow) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/list")
                ->clickLink("翌日")
                ->assertSee($tomorrow);
        });
    }
}
