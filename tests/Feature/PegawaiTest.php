<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class PegawaiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): Pegawai
    {
        $admin = Pegawai::create([
            'nama_pegawai' => 'Admin',
            'username'     => 'admin01',
            'password'     => Hash::make('password'),
            'id_role'      => 1,
        ]);
        Sanctum::actingAs($admin);
        return $admin;
    }

    public function test_can_get_all_pegawai()
    {
        $this->actingAsAdmin();
        Pegawai::create([
            'nama_pegawai' => 'Kasir Satu',
            'username'     => 'kasir01',
            'password'     => Hash::make('password'),
            'id_role'      => 3,
        ]);

        $this->getJson('/api/pegawai')
             ->assertStatus(200)
             ->assertJsonStructure(['data']);
    }

    public function test_can_create_kasir_account()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/pegawai', [
            'nama_pegawai'  => 'Kasir Baru',
            'username'      => 'kasirbaru',
            'password'      => 'password123',
            'id_role'       => 3,
            'tanggal_lahir' => '1995-05-10',
        ])
        ->assertStatus(201);

        $this->assertDatabaseHas('pegawai', [
            'username' => 'kasirbaru',
            'id_role'  => 3,
        ]);
    }

    public function test_password_is_hashed_on_create()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/pegawai', [
            'nama_pegawai' => 'Kasir Baru',
            'username'     => 'kasirbaru',
            'password'     => 'password123',
            'id_role'      => 3,
        ]);

        $pegawai = Pegawai::where('username', 'kasirbaru')->first();
        $this->assertTrue(Hash::check('password123', $pegawai->password));
        $this->assertNotEquals('password123', $pegawai->password);
    }

    public function test_username_must_be_unique()
    {
        $this->actingAsAdmin();
        Pegawai::create([
            'nama_pegawai' => 'Existing',
            'username'     => 'kasir01',
            'password'     => Hash::make('password'),
            'id_role'      => 3,
        ]);

        $this->postJson('/api/pegawai', [
            'nama_pegawai' => 'New Kasir',
            'username'     => 'kasir01',
            'password'     => 'password123',
            'id_role'      => 3,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username']);
    }

    public function test_can_update_pegawai_without_changing_password()
    {
        $admin   = $this->actingAsAdmin();
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Kasir Lama',
            'username'     => 'kasirlama',
            'password'     => Hash::make('oldpassword'),
            'id_role'      => 3,
        ]);
        $oldHash = $pegawai->password;

        $this->putJson("/api/pegawai/{$pegawai->id_pegawai}", [
            'nama_pegawai' => 'Kasir Updated',
            'username'     => 'kasirlama',
            'id_role'      => 3,
            'password'     => '',
        ])
        ->assertStatus(200);

        $this->assertDatabaseHas('pegawai', ['nama_pegawai' => 'Kasir Updated']);
        $updated = Pegawai::find($pegawai->id_pegawai);
        $this->assertEquals($oldHash, $updated->password);
    }

    public function test_can_delete_pegawai()
    {
        $this->actingAsAdmin();
        $pegawai = Pegawai::create([
            'nama_pegawai' => 'Kasir Delete',
            'username'     => 'kasirdel',
            'password'     => Hash::make('password'),
            'id_role'      => 3,
        ]);

        $this->deleteJson("/api/pegawai/{$pegawai->id_pegawai}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('pegawai', ['id_pegawai' => $pegawai->id_pegawai]);
    }

    public function test_role_must_be_1_or_3()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/pegawai', [
            'nama_pegawai' => 'Test',
            'username'     => 'testuser',
            'password'     => 'password123',
            'id_role'      => 5,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['id_role']);
    }
}