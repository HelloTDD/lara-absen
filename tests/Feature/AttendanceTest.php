<?php

namespace Tests\Feature;

use App\Models\Shift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceTest extends TestCase
{
    protected function actingAsUser(int $id)
    {
        $users = User::find($id);
        $this->actingAs($users);
        return $users;
    }
    /**
     * A basic feature test example.
     */
    public function test_access_index(): void
    {
        $this->actingAsUser(2);
        $response = $this->get('attendance/');

        $response->assertStatus(200);
    }

    /**
     * Summary of test_store_attendance_valid_data_not_overtime
     * User Login and Attendance Store with valid data and not overtime (from Calendar)
     */
    public function test_store_attendance_valid_data_not_overtime_from_calendar_input()
    {
        $user = $this->actingAsUser(2);
        // $shift = Shift::inRandomOrder()->first();
        $shift = Shift::find(1);
        $shift_id = $shift->id;
        $start_date = Carbon::today();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');
        $response = $this->postJson('calendar',[
            'user_id' => $user->id,
            'data' => $shift_id,
            'overtime' => null,
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date,
            'type' => 'shift'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'desc_shift' => null,
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date
        ]);

        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'MASUK'
        ]);
    }

    public function test_store_attendance_valid_data_overtime_from_calendar_input()
    {
        
        $user = $this->actingAsUser(2);
        // $shift = Shift::inRandomOrder()->first();
        $shift = Shift::find(1);
        $shift_id = $shift->id;
        $start_date = Carbon::today();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');
        $response = $this->postJson('calendar',[
            'user_id' => $user->id,
            'data' => $shift_id,
            'overtime' => 'LEMBUR',
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date,
            'type' => 'shift'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'desc_shift' => 'LEMBUR',
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date
        ]);

        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);
    }

    public function test_store_attendance_valid_data_not_overtime_from_user_shift_input()
    {
        
        $user = $this->actingAsUser(1);
        $shift = Shift::inRandomOrder()->first();
        $shift_id = $shift->id;
        $start_date = Carbon::today();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');
        $response = $this->postJson('user-shift/save',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'overtime' => null,
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'desc_shift' => null,
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date
        ]);

        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'MASUK'
        ]);
    }

    public function test_store_attendance_valid_data_overtime_from_user_shift_input()
    {
        
        $user = $this->actingAsUser(1);
        $shift = Shift::find(1);
        $shift = Shift::inRandomOrder()->first();
        $shift_id = $shift->id;
        $start_date = Carbon::today();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');
        $response = $this->postJson('user-shift/save',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'overtime' => 'LEMBUR',
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'desc_shift' => 'LEMBUR',
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date
        ]);

        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);
    }

    public function test_store_attendance_valid_data_overtime_when_data_shift_not_exists()
    {
        Carbon::setTestNow();
        $user = $this->actingAsUser(2);
        $shift = Shift::inRandomOrder()->first();
        $shift_id = $shift->id;
        Log::info(Carbon::setTestNow());
        $start_date = Carbon::today();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');

        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);
        
        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'desc_shift' => 'LEMBUR',
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $start_date->format('Y-m-d')
        ]);
    }

    public function test_check_in_outside_shift_time_should_be_flagged_as_out_of_shift()
    {
        //this unit test still bug

        $user = $this->actingAsUser(2);
        $shift = Shift::find(1); // Ensure this has check_in and check_out defined

        $start_date = Carbon::now();
        $end_date = $start_date->format('Y-m-d');

        // Buat shift harian
        $this->postJson('calendar', [
            'user_id' => $user->id,
            'data' => $shift->id,
            'overtime' => null,
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date,
            'type' => 'shift'
        ])->assertStatus(200);

        $this->assertDatabaseHas('user_shifts', [
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'desc_shift' => null,
            'start_date_shift' => $start_date->format('Y-m-d'),
        ]);

        // Set waktu absensi 5 jam setelah shift mulai
        $absenTime = $start_date->copy()->addHours(6);
        Carbon::setTestNow($absenTime);

        $response = $this->postJson('attendance/save', [
            'tanggal' => $absenTime->format('Y-m-d'),
            'time' => $absenTime->format('H:i:s'),
            'image' => 'data:image/jpeg;base64,' . base64_encode('dummy'),
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances', [
            'user_id' => $user->id,
            // 'shift_id' => $shift->id,
            'date' => $absenTime->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'ABSEN MASUK DILUAR JAM KERJA',
        ]);

        Carbon::setTestNow();
    }


    public function test_store_attendance_overtime_from_calendar_afternoon_to_night()
    {
        Carbon::setTestNow();
        $user = $this->actingAsUser(2);
        $shift = Shift::find(1); // days shift
        $shift_id = $shift->id;
        $start_date = Carbon::now();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');
        
        // $response = $this->postJson('calendar',[
        //     'user_id' => $user->id,
        //     'data' => $shift_id,
        //     'overtime' => null,
        //     'start_date' => $start_date->format('Y-m-d'),
        //     'end_date' => $end_date,
        //     'type' => 'shift'
        // ]);
        
        // $response->assertStatus(200);
        
        // $this->assertDatabaseHas('user_shifts',[
        //     'user_id' => $user->id,
        //     'shift_id' => $shift_id,
        //     'desc_shift' => null,
        //     'start_date_shift' => $start_date->format('Y-m-d'),
        //     'end_date_shift' => $end_date
        // ]);

        // $response = $this->postJson('attendance/save',[
        //     '_token' => csrf_token(),
        //     'tanggal' => $start_date->format('Y-m-d'),
        //     'time'    => $start_date->format('H:i:s'),
        //     // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
        //     'image' => 'tes.jpg',
        //     'lokasi' => "-7.5751763,110.897927",
        //     'action' => 'check_in',
        // ]);

        // $response->assertStatus(200);
        
        // $this->assertDatabaseHas('user_attendances',[
        //     'user_id' => $user->id,
        //     // 'shift_id' => $shift_id,
        //     'date' => $start_date->format('Y-m-d'),
        //     'latitude_in' => '-7.5751763',
        //     'longitude_in' => '110.897927',
        //     'desc_attendance' => 'MASUK'
        // ]);

        Carbon::setTestNow(Carbon::parse($start_date->copy()->addHours(8),'Asia/Jakarta'));
        
        $time_manipulate = Carbon::now('Asia/Jakarta');
        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $time_manipulate->format('Y-m-d'),
            'time'    => $time_manipulate->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'desc_shift' => 'LEMBUR',
            'start_date_shift' => $time_manipulate->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => 2,
            'date' => $time_manipulate->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);

        Carbon::setTestNow();
    }

    public function test_store_attendance_when_user_holiday_but_attendance_give_sign_as_overtime()
    {
        Carbon::setTestNow();
        $user = $this->actingAsUser(2);
        $shift = Shift::find(1); // days shift
        $shift_id = $shift->id;
        $start_date = Carbon::now();
        $end_date = $start_date->copy()->addDays(1)->format('Y-m-d');

        $response = $this->postJson('calendar',[
            'user_id' => $user->id,
            'data' => $shift_id,
            'overtime' => 'HOLIDAY',
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date,
            'type' => 'shift'
        ]);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'desc_shift' => 'HOLIDAY',
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $end_date
        ]);

        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format('H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
            'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);
    }

    public function test_store_attendance_invalid_data()
    {
        $this->actingAsUser(2);
        $response = $this->postJson('attendance/save', [
            '_token' => csrf_token(),
            'tanggal' => '',
            'time'    => '',
            'image' => '',
            'lokasi' => "",
            'action' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tanggal','time','lokasi','image','action']);
    }
}
