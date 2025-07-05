<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AttendanceCheckoutTest extends TestCase
{
    use WithoutMiddleware;
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

    public function test_store_attendance_valid_data_not_overtime_checkout()
    {
        $user = $this->actingAsUser(2);
        $start_date = Carbon::today();
        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format(format: 'H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
           'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_out',
        ]);

        $response->assertStatus(200);

        // $this->assertDatabaseHas('user_attendances',[
        //     'user_id' => $user->id,
        //     // 'shift_id' => $shift_id,
        //     'date' => $start_date->format('Y-m-d'),
        //     'latitude_in' => '-7.5751763',
        //     'longitude_in' => '110.897927',
        //     'desc_attendance' => 'PULANG'
        // ]);
    }

    public function test_store_attendance_valid_data_overtime_checkout()
    {
        $user = $this->actingAsUser(2);
        $start_date = Carbon::today();
        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format(format: 'H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
           'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_out',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'LEMBUR'
        ]);
    }
    
    public function test_check_out_outside_shift_time_should_be_flagged_as_out_of_shift()
    {
        $user = $this->actingAsUser(2);
        $start_date = Carbon::today();
        $response = $this->postJson('attendance/save',[
            '_token' => csrf_token(),
            'tanggal' => $start_date->format('Y-m-d'),
            'time'    => $start_date->format(format: 'H:i:s'),
            // 'image' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg'),
           'image' => 'tes.jpg',
            'lokasi' => "-7.5751763,110.897927",
            'action' => 'check_out',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('user_attendances',[
            'user_id' => $user->id,
            // 'shift_id' => $shift_id,
            'date' => $start_date->format('Y-m-d'),
            'latitude_in' => '-7.5751763',
            'longitude_in' => '110.897927',
            'desc_attendance' => 'ABSEN PULANG DILUAR JAM KERJA'
        ]);
    }
}
