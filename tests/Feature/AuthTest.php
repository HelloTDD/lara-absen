<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_access_login(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_cek_login_with_valid_data()
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        $responses = $this->post('/ceklogin', [
            'email' => $user->email,
            'password' => $password
        ]);

        $responses->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }

    public function test_cek_login_with_invalid_data()
    {
        $user = User::factory()->create([
            'email' => 'heheheh',
            'password' => Hash::make(123)
        ]);
        $responses = $this->postJson('/ceklogin',[
            'email' => $user->email,
            'password' => $user->password
        ]);
        $responses->assertStatus(422);
        $responses->assertJsonValidationErrors(['email']);
    }

    public function test_cek_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make(123)
        ]);

        $responses = $this->postJson('/ceklogin',[
            'email' => $user->email,
            'password' => '1234as'
        ]);
        $responses->assertStatus(302);

        // $this->assertAuthenticatedAs($user);
        $this->assertGuest();
    }

    public function test_cek_login_with_empty_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make(123)
        ]);

        $responses = $this->postJson('/ceklogin',[
            'email' => $user->email,
            'password' => ''
        ]);
        $responses->assertStatus(422);

        $responses->assertJsonValidationErrors(['password']);
        // $this->assertAuthenticatedAs($user);
        $this->assertGuest();
    }

    public function test_logout()
    {
        $password = 'secret123';
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        $responses = $this->post('/ceklogin', [
            'email' => $user->email,
            'password' => $password
        ]);

        $responses->assertStatus(302);
        $this->assertAuthenticatedAs($user);

        $responses_logout = $this->get('/logout');
        $responses_logout->assertStatus(200);
        $this->assertGuest();
    }
}