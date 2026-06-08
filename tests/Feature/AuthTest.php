<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Instruktur;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makePegawai(int $role): Pegawai
    {
        return Pegawai::create([
            'nama_pegawai' => 'Test Pegawai',
            'username'     => 'testuser',
            'password'     => Hash::make('password123'),
            'id_role'      => $role,
        ]);
    }

    public function test_admin_can_login_via_web()
    {
        $this->makePegawai(1);

        $this->postJson('/api/loginWeb', [
            'username' => 'testuser',
            'password' => 'password123',
        ])
        ->assertStatus(200)
        ->assertJsonStructure(['token', 'data'])
        ->assertJsonPath('data.id_role', 1);
    }

    public function test_kasir_can_login_via_web()
    {
        $this->makePegawai(3);

        $this->postJson('/api/loginWeb', [
            'username' => 'testuser',
            'password' => 'password123',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.id_role', 3);
    }

    public function test_login_fails_with_wrong_password()
    {
        $this->makePegawai(3);

        $this->postJson('/api/loginWeb', [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ])
        ->assertStatus(401)
        ->assertJsonPath('message', 'Username atau password salah.');
    }

    public function test_login_requires_username_and_password()
    {
        $this->postJson('/api/loginWeb', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_member_can_login_via_android()
    {
        Member::create([
            'nama_member' => 'Test Member',
            'username'    => 'member01',
            'password'    => Hash::make('password123'),
        ]);

        $this->postJson('/api/loginAndroid', [
            'username' => 'member01',
            'password' => 'password123',
        ])
        ->assertStatus(200)
        ->assertJsonPath('userType', 'member');
    }

    public function test_instruktur_can_login_via_android()
    {
        Instruktur::create([
            'nama_instruktur' => 'Test Instruktur',
            'username'        => 'instruktur01',
            'password'        => Hash::make('password123'),
        ]);

        $this->postJson('/api/loginAndroid', [
            'username' => 'instruktur01',
            'password' => 'password123',
        ])
        ->assertStatus(200)
        ->assertJsonPath('userType', 'instruktur');
    }

    public function test_authenticated_user_can_logout()
    {
        $pegawai = $this->makePegawai(3);
        Sanctum::actingAs($pegawai);

        $this->postJson('/api/logout')
             ->assertStatus(200)
             ->assertJsonPath('message', 'Logout berhasil.');
    }
}