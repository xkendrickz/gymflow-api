<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class KelasTest extends TestCase
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

    public function test_can_get_all_kelas()
    {
        $this->actingAsAdmin();
        Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);
        Kelas::create(['nama_kelas' => 'Zumba', 'tarif' => 75000]);

        $this->getJson('/api/kelas')
             ->assertStatus(200)
             ->assertJsonCount(2, 'data');
    }

    public function test_can_create_kelas()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/kelas', [
            'nama_kelas' => 'Pilates',
            'tarif'      => 60000,
        ])
        ->assertStatus(201);

        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'Pilates']);
    }

    public function test_create_kelas_requires_nama_kelas()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/kelas', ['tarif' => 50000])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['nama_kelas']);
    }

    public function test_can_update_kelas()
    {
        $this->actingAsAdmin();
        $kelas = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        $this->putJson("/api/kelas/{$kelas->id_kelas}", [
            'nama_kelas' => 'Yoga Advanced',
            'tarif'      => 75000,
        ])
        ->assertStatus(200);

        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'Yoga Advanced']);
    }

    public function test_can_delete_kelas()
    {
        $this->actingAsAdmin();
        $kelas = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        $this->deleteJson("/api/kelas/{$kelas->id_kelas}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('kelas', ['id_kelas' => $kelas->id_kelas]);
    }

    public function test_unauthenticated_user_cannot_access_kelas()
    {
        $this->getJson('/api/kelas')->assertStatus(401);
    }
}