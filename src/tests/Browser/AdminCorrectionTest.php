<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionApplication;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Rest;

class AdminCorrectionTest extends DuskTestCase
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

        $this->admin = User::create([
            "name" => "管理者ユーザー",
            "email" => "admin@example.com",
            "password" => bcrypt("password"),
            "role" => 2,
        ]);

        $this->user = User::create([
            "name" => "一般ユーザー",
            "email" => "user@example.com",
            "password" => bcrypt("password"),
            "role" => 1,
        ]);
    }

    /**
     * 承認待ちの修正申請が全て表示されている
     *
     * @return void
     */
    public function test_pending_corrections_are_displayed()
    {
        $attendance1 = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("17:00")->format("H:i"),
        ]);
        $attendance2 = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today()->addDay(),
            "clock_in_time" => Carbon::parse("10:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("18:00")->format("H:i"),
        ]);

        $rest1 = Rest::create([
            "attendance_id" => $attendance1->id,
            "start_time" => Carbon::parse("12:00")->format("H:i"),
            "end_time" => Carbon::parse("12:30")->format("H:i"),
        ]);
        $rest2 = Rest::create([
            "attendance_id" => $attendance2->id,
            "start_time" => Carbon::parse("13:00")->format("H:i"),
            "end_time" => Carbon::parse("13:45")->format("H:i"),
        ]);

        $rests_before_1 = [Carbon::parse($rest1->start_time)->diff(Carbon::parse($rest1->end_time))->format("%H:%I:%S")];
        $rests_before_2 = [Carbon::parse($rest2->start_time)->diff(Carbon::parse($rest2->end_time))->format("%H:%I:%S")];

        $correction1 = CorrectionApplication::create([
            "user_id" => $this->user->id,
            "attendance_id" => $attendance1->id,
            "clock_in_time_before" => $attendance1->clock_in_time,
            "clock_out_time_before" => $attendance1->clock_out_time,
            "rests_before" => $rests_before_1,
            "notes_before" => null,
            "clock_in_time_after" => Carbon::parse("09:05")->format("H:i"),
            "clock_out_time_after" => Carbon::parse("17:05")->format("H:i"),
            "rests_after" => Carbon::parse("00:35")->format("H:i"),
            "notes_after" => "メモ1修正",
            "is_approved" => null,
        ]);

        $correction2 = CorrectionApplication::create([
            "user_id" => $this->user->id,
            "attendance_id" => $attendance2->id,
            "clock_in_time_before" => $attendance2->clock_in_time,
            "clock_out_time_before" => $attendance2->clock_out_time,
            "rests_before" => $rests_before_2,
            "notes_before" => null,
            "clock_in_time_after" => Carbon::parse("10:05")->format("H:i"),
            "clock_out_time_after" => Carbon::parse("18:05")->format("H:i"),
            "rests_after" => Carbon::parse("00:50")->format("H:i"),
            "notes_after" => "メモ2修正",
            "is_approved" => null,
        ]);

        $this->browse(function (Browser $browser) use ($correction1, $correction2) {
            $browser->loginAs($this->admin)
                ->visit("/admin/stamp_correction_request/list?status=pending")
                ->assertSee($correction1->user->name)
                ->assertSee($correction2->user->name);
        });
    }

    /**
     * 承認済みの修正申請が全て表示されている
     *
     * @return void
     */
    public function test_approved_corrections_are_displayed()
    {
        $attendance1 = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("17:00")->format("H:i"),
        ]);
        $attendance2 = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today()->addDay(),
            "clock_in_time" => Carbon::parse("10:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("18:00")->format("H:i"),
        ]);

        $rest1 = Rest::create([
            "attendance_id" => $attendance1->id,
            "start_time" => Carbon::parse("12:00")->format("H:i"),
            "end_time" => Carbon::parse("12:30")->format("H:i"),
        ]);
        $rest2 = Rest::create([
            "attendance_id" => $attendance2->id,
            "start_time" => Carbon::parse("13:00")->format("H:i"),
            "end_time" => Carbon::parse("13:45")->format("H:i"),
        ]);

        $rests_before_1 = [Carbon::parse($rest1->start_time)->diff(Carbon::parse($rest1->end_time))->format("%H:%I:%S")];
        $rests_before_2 = [Carbon::parse($rest2->start_time)->diff(Carbon::parse($rest2->end_time))->format("%H:%I:%S")];

        $correction1 = CorrectionApplication::create([
            "user_id" => $this->user->id,
            "attendance_id" => $attendance1->id,
            "clock_in_time_before" => $attendance1->clock_in_time,
            "clock_out_time_before" => $attendance1->clock_out_time,
            "rests_before" => $rests_before_1,
            "notes_before" => null,
            "clock_in_time_after" => Carbon::parse("09:05")->format("H:i"),
            "clock_out_time_after" => Carbon::parse("17:05")->format("H:i"),
            "rests_after" => Carbon::parse("00:35")->format("H:i"),
            "notes_after" => "メモ1修正",
            "is_approved" => 1,
        ]);

        $correction2 = CorrectionApplication::create([
            "user_id" => $this->user->id,
            "attendance_id" => $attendance2->id,
            "clock_in_time_before" => $attendance2->clock_in_time,
            "clock_out_time_before" => $attendance2->clock_out_time,
            "rests_before" => $rests_before_2,
            "notes_before" => null,
            "clock_in_time_after" => Carbon::parse("10:05")->format("H:i"),
            "clock_out_time_after" => Carbon::parse("18:05")->format("H:i"),
            "rests_after" => Carbon::parse("00:50")->format("H:i"),
            "notes_after" => "メモ2修正",
            "is_approved" => 1,
        ]);

        $this->browse(function (Browser $browser) use ($correction1, $correction2) {
            $browser->loginAs($this->admin)
                ->visit("/admin/stamp_correction_request/list?status=approved")
                ->assertSee($correction1->user->name)
                ->assertSee($correction2->user->name);
        });
    }

    /**
     * 修正申請の詳細内容が正しく表示されている
     *
     * @return void
     */
    public function test_correction_details_are_displayed()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("17:00")->format("H:i"),
        ]);

        $rest = Rest::create([
            "attendance_id" => $attendance->id,
            "start_time" => Carbon::parse("12:00")->format("H:i"),
            "end_time" => Carbon::parse("12:30")->format("H:i"),
        ]);

        $rests_before = [Carbon::parse($rest->start_time)->diff(Carbon::parse($rest->end_time))->format("%H:%I")];

        $correction = CorrectionApplication::create([
            "user_id" => $this->user->id,
            "attendance_id" => $attendance->id,
            "clock_in_time_before" => $attendance->clock_in_time,
            "clock_out_time_before" => $attendance->clock_out_time,
            "rests_before" => $rests_before,
            "notes_before" => null,
            "clock_in_time_after" => Carbon::parse("09:05")->format("H:i"),
            "clock_out_time_after" => Carbon::parse("17:05")->format("H:i"),
            "rests_after" => json_encode([["start" => "00:35", "end" => "00:35"]]),
            "notes_after" => "メモ1修正",
            "is_approved" => 0,
        ]);

        $this->browse(function (Browser $browser) use ($correction) {
            $browser->loginAs($this->admin)
                ->visit("/admin/stamp_correction_request/detail/{$correction->attendance_id}")
                ->waitForText("勤怠詳細")
                ->assertSee("勤怠詳細")
                ->assertSee($correction->user->name)
                ->assertValue('input[name="clock_in_time_after"]', $correction->clock_in_time_after)
                ->assertValue('input[name="clock_out_time_after"]', $correction->clock_out_time_after)
                ->assertValue('textarea[name="notes_after"]', $correction->notes_after);
        });
    }

    /**
     * 修正申請の承認処理が正しく行われる
     *
     * @return void
     */
    public function test_correction_is_approved()
    {
        $attendance = Attendance::create([
            "user_id" => $this->user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("17:00")->format("H:i"),
        ]);

        $rest = Rest::create([
            "attendance_id" => $attendance->id,
            "start_time" => Carbon::parse("12:00")->format("H:i"),
            "end_time" => Carbon::parse("12:30")->format("H:i"),
        ]);

        $rests_before = [Carbon::parse($rest->start_time)->diff(Carbon::parse($rest->end_time))->format("%H:%I")];

        $correction = CorrectionApplication::create([
            "user_id" => $this->user->id,
            "attendance_id" => $attendance->id,
            "clock_in_time_before" => $attendance->clock_in_time,
            "clock_out_time_before" => $attendance->clock_out_time,
            "rests_before" => $rests_before,
            "notes_before" => null,
            "clock_in_time_after" => Carbon::parse("10:00")->format("H:i"),
            "clock_out_time_after" => Carbon::parse("18:00")->format("H:i"),
            "rests_after" => Carbon::parse("00:35")->format("H:i"),
            "notes_after" => "メモ1修正",
            "is_approved" => 0,
        ]);

        $this->browse(function (Browser $browser) use ($correction) {
            $browser->loginAs($this->admin)
                ->visit("/admin/stamp_correction_request/detail/{$correction->attendance_id}")
                ->waitForText("承認")
                ->assertSeeIn('.action-buttons', '承認')
                ->press("承認")
                ->assertPathIs("/admin/stamp_correction_request/list");
        });

        $this->assertDatabaseHas("attendances", [
            "id" => $attendance->id,
            "clock_in_time" => Carbon::parse("10:00")->format("H:i:s"),
            "clock_out_time" => Carbon::parse("18:00")->format("H:i:s"),
        ]);

        $this->assertDatabaseHas("correction_applications", [
            "id" => $correction->id,
            "is_approved" => 1,
        ]);
    }
}