<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Instruktur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class JadwalHarianTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): void
    {
        $admin = Pegawai::create([
            'nama_pegawai' => 'Admin',
            'username'     => 'admin01',
            'password'     => Hash::make('password'),
            'id_role'      => 1,
        ]);
        Sanctum::actingAs($admin);
    }

    private function seedJadwal(int $idInstruktur, string $tanggal): int
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
            'hari'           => $tanggal,
        ]);
    }

    public function test_returns_todays_jadwal_when_no_filter()
    {
        $this->actingAsAdmin();
        $instruktur = Instruktur::create([
            'nama_instruktur' => 'Instruktur A',
            'username'        => 'instruktur01',
            'password'        => Hash::make('password'),
        ]);
        $this->seedJadwal($instruktur->id_instruktur, Carbon::today()->toDateString());

        $this->getJson('/api/jadwalHarian')
             ->assertStatus(200)
             ->assertJsonCount(1, 'data');
    }

    public function test_filters_by_tanggal()
    {
        $this->actingAsAdmin();
        $instruktur = Instruktur::create([
            'nama_instruktur' => 'Instruktur A',
            'username'        => 'instruktur01',
            'password'        => Hash::make('password'),
        ]);

        $today    = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        $this->seedJadwal($instruktur->id_instruktur, $today);
        $this->seedJadwal($instruktur->id_instruktur, $tomorrow);

        $this->getJson("/api/jadwalHarian?tanggal={$today}")
             ->assertStatus(200)
             ->assertJsonCount(1, 'data')
             ->assertJsonPath('data.0.hari', $today);
    }

    public function test_filters_by_id_instruktur()
    {
        $this->actingAsAdmin();

        $instrukturA = Instruktur::create([
            'nama_instruktur' => 'Instruktur A',
            'username'        => 'instruktura',
            'password'        => Hash::make('password'),
        ]);
        $instrukturB = Instruktur::create([
            'nama_instruktur' => 'Instruktur B',
            'username'        => 'instrukturb',
            'password'        => Hash::make('password'),
        ]);

        $tanggal = Carbon::today()->toDateString();
        $this->seedJadwal($instrukturA->id_instruktur, $tanggal);
        $this->seedJadwal($instrukturB->id_instruktur, $tanggal);

        $this->getJson("/api/jadwalHarian?id_instruktur={$instrukturA->id_instruktur}")
             ->assertStatus(200)
             ->assertJsonCount(1, 'data')
             ->assertJsonPath('data.0.nama_instruktur', 'Instruktur A');
    }
}