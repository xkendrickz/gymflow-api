<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Member;
use App\Models\Instruktur;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class PresensiKelasTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsKasir(): void
    {
        Sanctum::actingAs(Pegawai::create([
            'nama_pegawai' => 'Kasir',
            'username'     => 'kasir01',
            'password'     => Hash::make('password'),
            'id_role'      => 3,
        ]));
    }

    private function seedBookingKelas(): int
    {
        $instruktur = Instruktur::factory()->create();
        $kelas      = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);
        $member     = Member::factory()->create();

        $jadwalUmum = DB::table('jadwal_umum')->insertGetId([
            'id_instruktur' => $instruktur->id_instruktur,
            'id_kelas'      => $kelas->id_kelas,
            'hari'          => 'monday',
            'jam'           => '07:00:00',
        ]);

        $jadwalHarian = DB::table('jadwal_harian')->insertGetId([
            'id_jadwal_umum' => $jadwalUmum,
            'hari'           => Carbon::today()->toDateString(),
        ]);

        return DB::table('booking_kelas')->insertGetId([
            'id_member'        => $member->id_member,
            'id_jadwal_harian' => $jadwalHarian,
            'jenis'            => 'reguler',
            'status'           => 0,
            'no_booking'       => 'BK-001', // add this
        ]);
    }

    public function test_can_get_presensi_kelas_list()
    {
        $this->actingAsKasir();

        $this->getJson('/api/presensiKelas')
             ->assertStatus(200)
             ->assertJsonStructure(['message', 'data']);
    }

    public function test_can_mark_member_hadir()
    {
        $this->actingAsKasir();
        $bookingId = $this->seedBookingKelas();

        $this->putJson("/api/presensiKelas/{$bookingId}")
             ->assertStatus(200)
             ->assertJsonPath('message', 'Presensi Kelas berhasil dicatat.');

        $this->assertDatabaseHas('booking_kelas', [
            'id_booking_kelas' => $bookingId,
            'status'           => 1,
        ]);

        $this->assertDatabaseHas('presensi_kelas', [
            'id_booking_kelas' => $bookingId,
        ]);
    }
}