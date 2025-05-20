<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Shift;
use App\Models\UserShift;
use App\Models\UserAttendance;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'user',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => '123 Main St',
            'gender' => 'Laki-laki',
            'birth_date' => '1990-01-01',
        ]);

        Shift::create([
            'shift_name' => 'Morning Shift',
            'check_in' => '2025-05-20 08:00:00',
            'check_out' => '2025-05-20 16:00:00',
        ]);

        UserShift::create([
            'user_id' => 1,
            'shift_id' => 1,
            'start_date_shift' => '2025-05-20',
            'end_date_shift' => '2025-05-20',
        ]);
    }
}
