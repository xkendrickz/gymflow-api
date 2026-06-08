<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Instruktur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class InstrukturTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): void
    {
        Sanctum::actingAs(Pegawai::create([
            'nama_pegawai' => 'Admin',
            'username'     => 'admin01',
            'password'     => Hash::make('password'),
            'id_role'      => 1,
        ]));
    }

    public function test_can_get_all_instruktur()
    {
        $this->actingAsAdmin();
        Instruktur::factory()->create();

        $this->getJson('/api/instruktur')
             ->assertStatus(200)
             ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_create_instruktur()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/instruktur', [
            'nama_instruktur' => 'Andi Wijaya',
            'tanggal_lahir'   => '1990-03-15',
            'username'        => 'andi01',
            'password'        => 'password123',
        ])
        ->assertStatus(201)
        ->assertJsonPath('success', true);

        $this->assertDatabaseHas('instruktur', ['username' => 'andi01']);
    }

    public function test_instruktur_password_is_hashed()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/instruktur', [
            'nama_instruktur' => 'Andi Wijaya',
            'tanggal_lahir'   => '1990-03-15',
            'username'        => 'andi01',
            'password'        => 'password123',
        ]);

        $instruktur = Instruktur::where('username', 'andi01')->first();
        $this->assertTrue(Hash::check('password123', $instruktur->password));
    }

    public function test_create_instruktur_requires_all_fields()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/instruktur', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors([
                 'nama_instruktur', 'tanggal_lahir', 'username', 'password'
             ]);
    }

    public function test_instruktur_username_must_be_unique()
    {
        $this->actingAsAdmin();
        Instruktur::factory()->create(['username' => 'taken01']);

        $this->postJson('/api/instruktur', [
            'nama_instruktur' => 'Other',
            'tanggal_lahir'   => '1990-03-15',
            'username'        => 'taken01',
            'password'        => 'password123',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username']);
    }

    public function test_can_show_instruktur()
    {
        $this->actingAsAdmin();
        $instruktur = Instruktur::factory()->create();

        $this->getJson("/api/instruktur/{$instruktur->id_instruktur}")
             ->assertStatus(200)
             ->assertJsonPath('data.nama_instruktur', $instruktur->nama_instruktur);
    }

    public function test_can_update_instruktur()
    {
        $this->actingAsAdmin();
        $instruktur = Instruktur::factory()->create();

        $this->putJson("/api/instruktur/{$instruktur->id_instruktur}", [
            'nama_instruktur' => 'Updated Name',
            'tanggal_lahir'   => '1990-03-15',
            'username'        => $instruktur->username,
        ])
        ->assertStatus(200)
        ->assertJsonPath('success', true);

        $this->assertDatabaseHas('instruktur', [
            'id_instruktur'   => $instruktur->id_instruktur,
            'nama_instruktur' => 'Updated Name',
        ]);
    }

    public function test_update_instruktur_without_changing_password()
    {
        $this->actingAsAdmin();
        $instruktur = Instruktur::factory()->create();
        $oldHash    = $instruktur->password;

        $this->putJson("/api/instruktur/{$instruktur->id_instruktur}", [
            'nama_instruktur' => 'Updated Name',
            'tanggal_lahir'   => '1990-03-15',
            'username'        => $instruktur->username,
        ]);

        $updated = Instruktur::find($instruktur->id_instruktur);
        $this->assertEquals($oldHash, $updated->password);
    }

    public function test_can_delete_instruktur()
    {
        $this->actingAsAdmin();
        $instruktur = Instruktur::factory()->create();

        $this->deleteJson("/api/instruktur/{$instruktur->id_instruktur}")
             ->assertStatus(200)
             ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('instruktur', [
            'id_instruktur' => $instruktur->id_instruktur,
        ]);
    }

    public function test_returns_404_for_missing_instruktur()
    {
        $this->actingAsAdmin();

        $this->getJson('/api/instruktur/99999')
             ->assertStatus(404);
    }
}