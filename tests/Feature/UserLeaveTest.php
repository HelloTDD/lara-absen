<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserLeave;
class UserLeaveTest extends TestCase
{
    protected function actingAsUser($user)
    {
        if($user == 'user'){
            $role = 0;
        } else {
            $role = 1;
        }

        Log::info($role);
        $users = User::factory()->create(['is_admin' => $role]);
        $this->actingAs($users);
        return $users;
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/user-leave');

        $response->assertStatus(302);
    }

    public function test_user_leave_index()
    {
        $user = $this->actingAsUser('user');
        $response = $this->get('/user-leave/user');

        $response->assertStatus(200);
        $response->assertViewIs('users-leave.index');
    }

    public function test_create_leave()
    {
        $user = $this->actingAsUser('admin');
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

        public function test_create_leave_from_user()
        {
            $user = $this->actingAsUser('user');
            $response = $this->post('/user-leave', [
                'start_date' => '2023-10-01',
                'end_date' => '2023-10-05',
                'description' => 'Sick leave'
            ]);

            // Since only admin can create, user should be forbidden or redirected
            $response->assertStatus(302); // or use 302 if redirecting, adjust as per your route logic

            $this->assertDatabaseMissing('user_leaves', [
                'user_id' => $user->id,
                'leave_date_start' => '2023-10-01',
                'leave_date_end' => '2023-10-05',
                'desc_leave' => 'Sick leave'
            ]);
        }

    public function test_update_leave()
    {
        $user = $this->actingAsUser('user');
        $leave = UserLeave::factory()->create(['user_id' => $user->id]);

        $response = $this->post('/user-leave/update' . $leave->id, [
            'description' => 'Updated leave'
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('user_leaves', [
            'id' => $leave->id
        ]);
    }

    public function test_delete_leave()
    {
        $user = $this->actingAsUser('user'); 
        $leave = UserLeave::factory()->create(['user_id' => $user->id]);

        $response = $this->delete('/user-leave/delete/'.$leave->id);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('user_leaves', [
            'id' => $leave->id
        ]);
    }

    public function test_approve_leave()
    {
        $dummy = User::factory()->create();
        $this->actingAsUser('admin');
        $leave = UserLeave::factory()->create([
            'user_id' => $dummy->id,
            'status' => 'pending'
        ]);

        $response = $this->get("/user-leave/approve/{$leave->id}");

        $response->assertStatus(302);

        $this->assertDatabaseHas('user_leaves',[
            'user_id' => $dummy->id,
            'status' => 'approved'
        ]);
    }

    public function test_approve_leave_with_not_exists_data()
    {
        $this->actingAsUser('admin');

        $response = $this->get("/user-leave/approve/9999999");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('user_leaves',[
            'id' => 9999999,
            'status' => 'approved'
        ]);
    }

    public function test_approve_leave_from_user()
    {
        $dummy = User::factory()->create();
        $this->actingAsUser('user');
        $leave = UserLeave::factory()->create([
            'user_id' => $dummy->id,
            'status' => 'pending'
        ]);

        $response = $this->get("/user-leave/approve/{$leave->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('user_leaves',[
            'user_id' => $dummy->id,
            'status' => 'approve'
        ]);
    }
    
    public function test_reject_leave()
    {
        $dummy = User::factory()->create();
        $this->actingAsUser('admin');
        $leave = UserLeave::factory()->create([
            'user_id' => $dummy->id,
            'status' => 'pending'
        ]);

        $response = $this->get("/user-leave/reject/{$leave->id}");

        $response->assertStatus(302);

        $this->assertDatabaseHas('user_leaves',[
            'user_id' => $dummy->id,
            'status' => 'rejected'
        ]);
    }
    public function test_reject_leave_with_not_exists_data()
    {
        $this->actingAsUser('admin');

        $response = $this->get("/user-leave/reject/9999999");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('user_leaves',[
            'user_id' => 9999999,
            'status' => 'rejected'
        ]);
    }

    public function test_reject_leave_from_user()
    {
        $dummy = User::factory()->create();
        $this->actingAsUser('user');
        $leave = UserLeave::factory()->create([
            'user_id' => $dummy->id,
            'status' => 'pending'
        ]);

        $response = $this->get("/user-leave/reject/{$leave->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('user_leaves',[
            'user_id' => $dummy->id,
            'status' => 'rejected'
        ]);
    }

    public function test_create_leave_with_invalid_data ()
    {
        $this->actingAsUser('admin');
        $response = $this->postJson('/user-leave', [
            'start_date' => 'invalid',
            'end_date' => 'invalid',
            'description' => 'invalid',
            'status' => 'invalid'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start_date','end_date','status']);
        $this->assertDatabaseMissing('user_leaves', [
            'leave_date_start' => 'invalid',
            'leave_date_end' => 'invalid',
            'desc_leave' => 'invalid',
            'status' => 'invalid'
        ]);
    }

    public function test_create_leave_with_empty_data ()
    {
        $this->actingAsUser('admin');
        $response = $this->postJson('/user-leave', [
            'start_date' => null,
            'end_date' => null,
            'description' => null,
            'status' => null
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['start_date','end_date','description']);
        $this->assertDatabaseMissing('user_leaves', [
            'leave_date_start' => null,
            'leave_date_end' => null,
            'desc_leave' => null,
            'status' => null
        ]);
    }
}
