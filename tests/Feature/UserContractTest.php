<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserContractTest extends TestCase
{
    protected function actingAsUser($user)
    {
        if($user == 'user'){
            $role = 0;
        } else {
            $role = 1;
        }

        $users = User::factory()->create(['is_admin' => $role]);
        $this->actingAs($users);
        return $users;
    }
    /**
     * A basic feature test example.
     */
    public function test_index()
    {
        $user = $this->actingAsUser('user');
        $response = $this->get('/user-contract');

        $response->assertStatus(200);
        $response->assertViewIs('users-contract.index');
    }

    public function test_create_contract_valid_data()
    {
        $user = $this->actingAsUser('admin');
        $dummy = User::factory()->create([
            'is_admin' => 0
        ]);
        $response = $this->post('user-contract',[
            'user_id' => $dummy->id,
            'desc_constract' => 'tes contract',
            'start_contract_date' => '2022-01-01',
            'end_contract_date' => '2022-01-31',
            'file' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg')
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('user_contracts',[
            'user_id' => $dummy->id,
            'name' => $dummy->name,
            'desc_contract' => 'tes contract',
            'start_contract_date' => '2022-01-01',
            'end_contract_date' => '2022-01-31',
        ]);
    }

    public function test_create_contract_invalid_data()
    {
        $user = $this->actingAsUser('admin');
        $response = $this->postJson('user-contract',[
            'user_id' => 'invalid',
            'desc_constract' => 'tes contract',
            'start_contract_date' => 'invalid',
            'end_contract_date' => 'invalid',
            'file' => 'invalid'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user_id','start_contract_date','end_contract_date','file']);
        $this->assertDatabaseMissing('user_contracts',[
            'user_id' => 'invalid',
            'desc_contract' => 'tes contract',
            'start_contract_date' => 'invalid',
            'end_contract_date' => 'invalid',
        ]);
    }

    public function test_create_contract_valid_data_from_user()
    {
        $user = $this->actingAsUser('user');
        $dummy = User::factory()->create([
            'is_admin' => 0
        ]);
        $response = $this->post('user-contract',[
            'user_id' => $dummy->id,
            'desc_constract' => 'tes contract',
            'start_contract_date' => '2022-01-01',
            'end_contract_date' => '2022-01-31',
            'file' => UploadedFile::fake()->create('tes.jpg', 1000, 'image/jpeg')
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseMissing('user_contracts',[
            'user_id' => $dummy->id,
            'name' => $dummy->name,
            'desc_contract' => 'tes contract',
            'start_contract_date' => '2022-01-01',
            'end_contract_date' => '2022-01-31',
        ]);
    }
}
