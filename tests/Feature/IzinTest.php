<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Instruktur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

class IzinTest extends TestCase
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

    private function createJadwalHarian(int $idInstruktur): int
    {
        $kelas = DB::table('kelas')->insertGetId([
            'nama_kelas' => 'Yoga',
            'tarif'      => 50000,
        ]);
        $jadwalUmum = DB::table('jadwal_umum')->insertGetId([
            'id_instruktur' => $idInstruktur,
            'id_kelas'      => $kelas,
            'hari'          => 'monday',
            'jam'           => '07:00:00',
        ]);
        return DB::table('jadwal_harian')->insertGetId([
            'id_jadwal_umum' => $jadwalUmum,
            'hari'           => now()->addDays(7)->toDateString(),
        ]);
    }

    public function test_instruktur_can_submit_izin()
    {
        $this->actingAsAdmin();
        $instruktur  = Instruktur::create([
            'nama_instruktur' => 'Instruktur Test',
            'username'        => 'instruktur01',
            'password'        => Hash::make('password'),
        ]);
        $idJadwal = $this->createJadwalHarian($instruktur->id_instruktur);

        $this->postJson('/api/izin', [
            'id_instruktur'    => $instruktur->id_instruktur,
            'id_jadwal_harian' => $idJadwal,
            'detail_izin'      => 'Sakit demam',
            'tanggal_izin'     => now()->addDays(7)->toDateString(),
        ])
        ->assertStatus(201);

        $this->assertDatabaseHas('izin', [
            'id_instruktur' => $instruktur->id_instruktur,
            'konfirmasi'    => 0,
        ]);
    }

    public function test_admin_can_confirm_izin()
    {
        $this->actingAsAdmin();

        // create referenced records first
        $instruktur = Instruktur::create([
            'nama_instruktur' => 'Instruktur Test',
            'username'        => 'instruktur01',
            'password'        => Hash::make('password'),
        ]);

        $idJadwal = $this->createJadwalHarian($instruktur->id_instruktur);

        $izinId = DB::table('izin')->insertGetId([
            'id_instruktur'    => $instruktur->id_instruktur,
            'id_jadwal_harian' => $idJadwal,
            'detail_izin'      => 'Sakit',
            'tanggal_izin'     => now()->toDateString(),
            'konfirmasi'       => 0,
        ]);

        $this->putJson("/api/izin/{$izinId}")
            ->assertStatus(200)
            ->assertJsonPath('message', 'Izin berhasil dikonfirmasi.');

        $this->assertDatabaseHas('izin', [
            'id_izin'    => $izinId,
            'konfirmasi' => 1,
        ]);
    }

    public function test_izin_requires_all_fields()
    {
        $this->actingAsAdmin();

        $this->postJson('/api/izin', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors([
                 'id_instruktur',
                 'id_jadwal_harian',
                 'detail_izin',
                 'tanggal_izin',
             ]);
    }
}