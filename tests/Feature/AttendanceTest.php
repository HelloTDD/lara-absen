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
    protected function actingAsUser()
    {
        $users = User::find(2);
        $this->actingAs($users);
        return $users;
    }
    /**
     * A basic feature test example.
     */
    public function test_access_index(): void
    {
        $this->actingAsUser();
        $response = $this->get('attendance/');

        $response->assertStatus(200);
    }

    /**
     * Summary of test_store_attendance_valid_data_not_overtime
     * User Login and Attendance Store with valid data and not overtime (from Calendar)
     */
    public function test_store_attendance_valid_data_not_overtime_from_calendar_input()
    {
        
        $user = $this->actingAsUser();
        $shift = Shift::inRandomOrder()->first();
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
            'shift_id' => $shift_id,
            'date' => '2025-06-26',
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'MASUK'
        ]);
    }

    public function test_store_attendance_valid_data_overtime_from_calendar_input()
    {
        
        $user = $this->actingAsUser();
        $shift = Shift::inRandomOrder()->first();
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
            'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);
    }

    public function test_store_attendance_valid_data_overtime_when_data_shift_not_exists()
    {
        
        $user = $this->actingAsUser();
        $shift = Shift::inRandomOrder()->first();
        $shift_id = $shift->id;
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
            'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR MASUK'
        ]);
        
        $this->assertDatabaseHas('user_shifts',[
            'user_id' => $user->id,
            'shift_id' => $shift_id,
            'desc_shift' => 'LEMBUR',
            'start_date_shift' => $start_date->format('Y-m-d'),
            'end_date_shift' => $start_date->format('Y-m-d')
        ]);
    }

    public function test_store_attendance_invalid_data()
    {
        $this->actingAsUser();
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
