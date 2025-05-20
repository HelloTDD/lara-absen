<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserSalary;

class UserSalaryTest extends TestCase
{
    protected function actingAsAdmin()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $this->actingAs($user);
        return $user;
    }

    public function test_create_user_salary_with_valid_data()
    {
        $user = $this->actingAsAdmin();
        $response = $this->post('/user-salaries', [
            'user_id' => $user->id,
            'salary_basic' => 50000,
            'salary_allowance' => 10000,
            'salary_bonus' => 5000,
            'salary_holiday' => 2000,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('user_salaries', [
            'user_id' => $user->id,
            'salary_basic' => 50000,
            'salary_allowance' => 10000,
            'salary_bonus' => 5000,
            'salary_holiday' => 2000,
        ]);
    }

    public function test_create_user_salary_with_invalid_data()
    {
        
        $user = $this->actingAsAdmin();
        $response = $this->postJson('/user-salaries', [
            'user_id' => null,
            'salary_basic' => 'invalid',
            'salary_allowance' => 'invalid',
            'salary_bonus' => 'invalid',
            'salary_holiday' => 'invalid',
        ]);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrors(['user_id', 'salary_basic', 'salary_allowance', 'salary_bonus', 'salary_holiday']);
        $this->assertDatabaseMissing('user_salaries', [
            'user_id' => null,
            'salary_basic' => 'invalid',
            'salary_allowance' => 'invalid',
            'salary_bonus' => 'invalid',
            'salary_holiday' => 'invalid',
        ]);
    }

    public function test_create_user_salary_with_empty_data()
    {
        $user = $this->actingAsAdmin();
        $response = $this->postJson('/user-salaries', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id', 'salary_basic', 'salary_allowance', 'salary_bonus', 'salary_holiday']);
        // $this->assertDatabaseMissing('user_salaries', []);
    }

    public function test_create_user_salary_with_non_existent_user()
    {
        $user = $this->actingAsAdmin();
        $response = $this->postJson('/user-salaries', [
            'user_id' => 99999,
            'salary_basic' => 50000,
            'salary_allowance' => 10000,
            'salary_bonus' => 5000,
            'salary_holiday' => 2000,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id']);
        $this->assertDatabaseMissing('user_salaries', [
            'user_id' => 99999,
            'salary_basic' => 5000,
            'salary_allowance' => 1000,
            'salary_bonus' => 500,
            'salary_holiday' => 200,
        ]);
    }

    public function test_get_user_salary()
    {
        // $user = User::factory()->create();
        // $salary = UserSalary::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/user-salaries');
        $response->assertStatus(200);
        $response->assertViewIs('users-salary.index');
    }
}
