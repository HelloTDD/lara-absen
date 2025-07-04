<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
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

        Shift::create([
            'shift_name' => 'Pagi',
            'check_in' => '08:30:00',
            'check_out' => '17:00:00',
        ]);

        Shift::create([
            'shift_name' => 'Malam',
            'check_in' => '19:00:00',
            'check_out' => '04:00:00',
        ]);

        Shift::create([
            'shift_name' => 'Lembur',
            'check_in' => '00:00:00',
            'check_out' => '23:59:00',
        ]);

        Role::create([
            'role_name' => 'Staff',
            'description' => 'Staff',
            'job_description' => '["test","tes3","test2"]',
        ]);

        Role::create([
            'role_name' => 'IT Support',
            'description' => 'IT Support',
            'job_description' => '["test","tes3","test2"]',
        ]);

        Role::create([
            'role_name' => 'Programmer',
            'description' => 'Programmer',
            'job_description' => '["test","tes3","test2"]',
        ]);

        User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => '123 Main St',
            'gender' => 'Laki-laki',
            'birth_date' => '1990-01-01',
            'is_admin' => true,
            'leave' => 12,
            'role_id' => 1, // Assuming the role_id for 'karyawan' is 1
        ]);

         User::create([
            'name' => 'user',
            'username' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => '123 Main St',
            'gender' => 'Laki-laki',
            'birth_date' => '1990-01-01',
            'is_admin' => false,
            'leave' => 12,
            'role_id' => 1, // Assuming the role_id for 'karyawan' is 1
        ]);


        // Shift::create([
        //     'shift_name' => 'Morning Shift',
        //     'check_in' => '08:00:00',
        //     'check_out' => '16:00:00',
        // ]);

        // UserShift::create([
        //     'user_id' => 1,
        //     'shift_id' => 1,
        //     'start_date_shift' => '2025-05-20',
        //     'end_date_shift' => '2025-05-20',
        // ]);
    }
}
