<?php

namespace Tests\Browser;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\CorrectionApplication;

class AttendanceModificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     *
     * @return array
     */
    public function prepareTestData(): array
    {
        $user = User::where("email", "test@example.com")->first();
        $admin = User::where("email", "test@example.co.jp")->first();

        $attendance = Attendance::create([
            "user_id" => $user->id,
            "date" => Carbon::today(),
            "clock_in_time" => Carbon::parse("09:00")->format("H:i"),
            "clock_out_time" => Carbon::parse("17:00")->format("H:i"),
        ]);
        Rest::create([
            "attendance_id" => $attendance->id,
            "start_time" => Carbon::parse("12:00")->format("H:i"),
            "end_time" => Carbon::parse("13:00")->format("H:i"),
        ]);

        return [
            $user,
            $admin,
            $attendance,
        ];
    }

    /**
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
     *
     * @return void
     */
    public function test_clock_in_after_clock_out_error_message()
    {
        $this->browse(function (Browser $browser) {
            [$user, , $attendance] = $this->prepareTestData();

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->type("clock_in_time_after", "18:00")
                ->type("clock_out_time_after", "17:00")
                ->press("修正")
                ->assertSee("出勤時間もしくは退勤時間が不適切な値です。");
        });
    }

    /**
     * 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
     * @return void
     */
    public function test_rest_start_after_clock_out_error_message()
    {
        $this->browse(function (Browser $browser) {
            [$user, , $attendance] = $this->prepareTestData();

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->type("rests_after[0][start]", "18:00")
                ->type("clock_out_time_after", "17:00")
                ->press("修正")
                ->assertSee("休憩時間が不適切な値です。");
        });
    }

    /**
     * 備考欄が未入力の場合のエラーメッセージが表示される
     * @return void
     */
    public function test_notes_after_required_error_message()
    {
        $this->browse(function (Browser $browser) {
            [$user, , $attendance] = $this->prepareTestData();

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->clear("notes_after")
                ->press("修正")
                ->assertSee("備考を記入してください。");
        });
    }

    /**
     * 修正申請処理が実行され、管理者の承認画面に表示される
     * @return void
     */
    public function test_request_modify_and_approval()
    {
        $this->browse(function (Browser $browser) {
            [$user, $admin, $attendance] = $this->prepareTestData();

            // ユーザーが修正申請を行う
            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->type("clock_in_time_after", "10:00")
                ->type("notes_after", "修正申請のテスト")
                ->press("修正")
                ->logout();

            // 管理者としてログインし、承認リストを確認
            $browser->loginAs($admin)
                ->visit("/admin/stamp_correction_request/list")
                ->waitForText("承認待ち")
                ->assertSee("承認待ち")
                ->assertSee($user->name)
                ->assertSee("修正申請のテスト");
        });
    }

    /**
     * 自分の申請が承認待ちに表示されることを確認する
     * @return void
     */
    public function test_pending_requests_are_visible_in_user_list()
    {
        $this->browse(function (Browser $browser) {
            [$user, , $attendance] = $this->prepareTestData();

            $browser->loginAs($user)
                ->visit("/attendance/{$attendance->id}")
                ->type("clock_in_time_after", "10:00")
                ->type("notes_after", "修正申請のテスト")
                ->press("修正");

            $browser->visit('/stamp_correction_request/list')
                ->waitForText('承認待ち')
                ->assertSee('承認待ち')
                ->assertSee('修正申請のテスト');
        });
    }

    /**
     * 「承認済み」に管理者が承認した修正申請が全て表示されている
     * @return void
     */
    public function test_approved_requests_are_visible_in_user_list()
    {
        $this->browse(function (Browser $browser) {
            [$user, , $attendance] = $this->prepareTestData();

            CorrectionApplication::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'is_approved' => 1,
                'clock_in_time_before' => $attendance->clock_in_time,
                'clock_in_time_after' => '09:30:00',
            ]);

            $browser->loginAs($user)
                ->visit('/stamp_correction_request/list')
                ->waitForText('承認済み')
                ->assertSee('承認済み');
        });
    }

    /**
     * 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
     * @return void
     */
    public function test_click_detail_button_navigates_to_detail_page()
    {
        $this->browse(function (Browser $browser) {
            [$user, , $attendance] = $this->prepareTestData();

            $request = CorrectionApplication::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'is_approved' => null,
                'clock_in_time_before' => $attendance->clock_in_time,
                'clock_in_time_after' => '09:30:00',
            ]);

            $browser->loginAs($user)
                ->visit('/stamp_correction_request/list')
                ->waitForLink('詳細')
                ->clickLink('詳細')
                ->assertPathIs("/attendance/{$attendance->id}");
        });
    }
}