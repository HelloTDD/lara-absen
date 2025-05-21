<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserLeave;
class UserLeaveTest extends TestCase
{
    protected function actingAsUser()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $this->actingAs($user);
        return $user;
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/user-leave');

        $response->assertStatus(200);
    }

    public function test_user_leave_index()
    {
        $user = $this->actingAsUser();
        $response = $this->get('/user-leave/user');

        $response->assertStatus(200);
        $response->assertViewIs('users-leave.index');
    }

    public function test_create_leave()
    {
        $user = $this->actingAsUser();
        $response = $this->post('/user-leave', [
            'start_date' => '2023-10-01',
            'end_date' => '2023-10-05',
            'description' => 'Sick leave'
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('user_leaves', [
            'user_id' => $user->id,
            'leave_date_start' => '2023-10-01',
            'leave_date_end' => '2023-10-05',
            'desc_leave' => 'Sick leave'
        ]);
    }

    public function test_update_leave()
    {
        $user = $this->actingAsUser();
        $leave = UserLeave::factory()->create(['user_id' => $user->id]);

        $response = $this->put('/user-leave/' . $leave->id, [
            'description' => 'Updated leave'
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('user_leaves', [
            'id' => $leave->id,
            'desc_leave' => 'Updated leave'
        ]);
    }
}
