<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;
use App\Models\User;

class AttendanceTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * 勤怠打刻画面の現在日時がサーバーの日時と一致することを確認するテスト
     *
     * @return void
     */

    public function test_ui_date_matches_current_date()
    {
        $this->browse(function (Browser $browser) {
            $user = User::first();
            $browser->loginAs($user);
            $browser->visit('/attendance');
            $browser->pause(1000);

            $uiDate = $browser->text('#currentDate');

            $expectedDate = Carbon::now()->isoFormat('YYYY年M月D日(ddd)');

            $this->assertEquals($expectedDate, $uiDate);
        });
    }
}