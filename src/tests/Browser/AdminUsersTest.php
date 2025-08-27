<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminUsersTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @var User
     */
    protected $admin;

    /**
     * @var User
     */
    protected $user1;

    /**
     * @var User
     */
    protected $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 2, // 管理者
        ]);

        $this->user1 = User::create([
            'name' => '一般ユーザー1',
            'email' => 'attendance@example.com',
            'password' => bcrypt('password'),
            'role' => 1, // 一般ユーザー
        ]);

        $this->user2 = User::create([
            'name' => '一般ユーザー2',
            'email' => 'attendane@example.co.nz',
            'password' => bcrypt('password'),
            'role' => 1, // 一般ユーザー
        ]);
    }

    /**
     * 管理者ユーザーが全ての一般ユーザーの氏名とメールアドレスを確認できる
     *
     * @return void
     */
    public function test_all_users_details_are_correctly_displayed()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                ->visit("/admin/staff/list")
                ->assertSee($this->user1->name)
                ->assertSee($this->user1->email)
                ->assertSee($this->user2->name)
                ->assertSee($this->user2->email);
        });
    }

    /**
     * 選択したユーザーの勤怠情報が正しく表示される
     *
     * @return void
     */
    public function test_selected_users_attendance_is_correctly_displayed()
    {
        Attendance::create([
            'user_id' => $this->user1->id,
            'date' => Carbon::today(),
            'clock_in_time' => Carbon::parse('09:00'),
            'clock_out_time' => Carbon::parse('17:00'),
        ]);

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $this->browse(function (Browser $browser) use ($year, $month) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/staff/{$this->user1->id}/{$year}/{$month}")
                ->waitForText(Carbon::today()->isoFormat('MM/DD(ddd)'))
                ->assertSee($this->user1->name)
                ->assertSee(Carbon::today()->isoFormat('YYYY/MM'));
        });
    }

    /**
     * 「前月」を押下した時に表示月の前月の情報が表示される
     *
     * @return void
     */
    public function test_previous_month_button_displays_previous_month_data()
    {
        Attendance::create([
            'user_id' => $this->user1->id,
            'date' => Carbon::today()->subMonth(),
            'clock_in_time' => Carbon::parse('09:00'),
            'clock_out_time' => Carbon::parse('17:00'),
        ]);

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $this->browse(function (Browser $browser) use ($year, $month) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/staff/{$this->user1->id}/{$year}/{$month}")
                ->waitForText('前月')
                ->clickLink('前月')
                ->waitForText(Carbon::today()->subMonth()->isoFormat('YYYY/MM'))
                ->assertSee(Carbon::today()->subMonth()->isoFormat('YYYY/MM'));
        });
    }

    /**
     * 「翌月」を押下した時に表示月の翌月の情報が表示される
     *
     * @return void
     */
    public function test_next_month_button_displays_next_month_data()
    {
        Attendance::create([
            'user_id' => $this->user1->id,
            'date' => Carbon::today()->addMonth(),
            'clock_in_time' => Carbon::parse('09:00'),
            'clock_out_time' => Carbon::parse('17:00'),
        ]);

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $this->browse(function (Browser $browser) use ($year, $month) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/staff/{$this->user1->id}/{$year}/{$month}")
                ->waitForText('翌月')
                ->clickLink('翌月')
                ->waitForText(Carbon::today()->addMonth()->isoFormat('YYYY/MM'))
                ->assertSee(Carbon::today()->addMonth()->isoFormat('YYYY/MM'));
        });
    }

    /**
     * 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
     *
     * @return void
     */
    public function test_detail_button_redirects_to_attendance_detail_page()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user1->id,
            'date' => Carbon::today(),
            'clock_in_time' => Carbon::parse('09:00'),
            'clock_out_time' => Carbon::parse('17:00'),
        ]);

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;

        $this->browse(function (Browser $browser) use ($attendance, $year, $month) {
            $browser->loginAs($this->admin)
                ->visit("/admin/attendance/staff/{$this->user1->id}/{$year}/{$month}")
                ->waitForText(Carbon::today()->isoFormat('MM/DD(ddd)'))
                ->clickLink("詳細")
                ->assertPathIs("/admin/attendance/{$attendance->id}")
                ->assertSee('勤怠詳細');
        });
    }
}