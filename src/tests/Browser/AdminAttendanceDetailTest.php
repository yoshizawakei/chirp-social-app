<?php

namespace Tests\Browser;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\CorrectionApplication;

class AdminAttendanceDetailTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @var User
     */
    protected $admin;

    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // 管理者ユーザーを作成
        $this->admin = User::create([
            "name" => "Admin User",
            "email" => "admin@example.com",
            "password" => bcrypt("password"),
            "role" => 2,
        ]);
        // 一般ユーザーを作成
        $this->user = User::create([
            "name" => "General User",
            "email" => "user@example.com",
            "password" => bcrypt("password"),
            "role" => 1,
        ]);
    }

    /**
     * 勤怠詳細画面に表示されるデータが選択したものになっている
     *
     * @return void
     */
    public function test_details_are_correctly_displayed()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00"),
            "clock_out_time" => Carbon::parse("17:00"),
        ]);

        $this->browse(function (Browser $browser) use ($attendance) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/{$attendance->id}")
                ->waitForText("勤怠詳細")
                ->assertSee("勤怠詳細")
                ->assertSee($this->user->name)
                ->assertSee(Carbon::today()->isoFormat("YYYY年"))
                ->assertSee(Carbon::today()->isoFormat("M月D日"))
                ->assertInputValue("clock_in_time_after", "09:00")
                ->assertInputValue("clock_out_time_after", "17:00");
        });
    }

    /**
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     *
     * @return void
     */
    public function test_clock_in_after_clock_out_error_message()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00"),
            "clock_out_time" => Carbon::parse("17:00"),
        ]);

        $this->browse(function (Browser $browser) use ($attendance) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/{$attendance->id}")
                ->type("clock_in_time_after", "18:00")
                ->type("clock_out_time_after", "17:00")
                ->press("修正")
                ->waitForText("出勤時間もしくは退勤時間が不適切な値です。")
                ->assertSee("出勤時間もしくは退勤時間が不適切な値です。");
        });
    }

    /**
     * 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     *
     * @return void
     */
    public function test_rest_start_after_clock_out_error_message()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00"),
            "clock_out_time" => Carbon::parse("17:00"),
        ]);

        $this->browse(function (Browser $browser) use ($attendance) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/{$attendance->id}")
                ->type("rests_after[0][start]", "18:00")
                ->press("修正")
                ->waitForText("休憩時間が不適切な値です。")
                ->assertSee("休憩時間が不適切な値です。");
        });
    }

    /**
     * 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
     *
     * @return void
     */
    public function test_rest_end_after_clock_out_error_message()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00"),
            "clock_out_time" => Carbon::parse("17:00"),
        ]);

        $this->browse(function (Browser $browser) use ($attendance) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/{$attendance->id}")
                ->type("rests_after[0][end]", "18:00")
                ->press("修正")
                ->waitForText("休憩時間もしくは退勤時間が不適切な値です")
                ->assertSee("休憩時間もしくは退勤時間が不適切な値です");
        });
    }

    /**
     * 備考欄が未入力の場合のエラーメッセージが表示される
     *
     * @return void
     */
    public function test_notes_after_required_error_message()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00"),
            "clock_out_time" => Carbon::parse("17:00"),
        ]);

        $this->browse(function (Browser $browser) use ($attendance) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/{$attendance->id}")
                ->clear("notes_after")
                ->press("修正")
                ->waitForText("備考を記入してください。")
                ->assertSee("備考を記入してください。");
        });
    }
}