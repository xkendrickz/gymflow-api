<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class PresensiGymTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsKasir(): Pegawai
    {
        $kasir = Pegawai::create([
            'nama_pegawai' => 'Kasir',
            'username'     => 'kasir01',
            'password'     => Hash::make('password'),
            'id_role'      => 3,
        ]);
        Sanctum::actingAs($kasir);
        return $kasir;
    }

    private function createBooking(int $idMember, int $status = 0): int
    {
        return DB::table('booking_gym')->insertGetId([
            'id_member'  => $idMember,
            'tanggal'    => Carbon::today()->toDateString(),
            'slot_waktu' => '07:00:00',
            'status'     => $status,
        ]);
    }

    public function test_can_get_presensi_gym_list()
    {
        $this->actingAsKasir();

        $this->getJson('/api/presensiGym')
             ->assertStatus(200)
             ->assertJsonStructure(['data']);
    }

    public function test_can_mark_member_as_hadir()
    {
        $this->actingAsKasir();
        $member    = Member::factory()->create();
        $bookingId = $this->createBooking($member->id_member);

        $this->putJson("/api/presensiGym/{$bookingId}")
             ->assertStatus(200)
             ->assertJsonPath('message', 'Presensi Gym berhasil dicatat.');

        $this->assertDatabaseHas('booking_gym', [
            'id_booking_gym' => $bookingId,
            'status'         => 1,
        ]);

        $this->assertDatabaseHas('presensi_gym', [
            'id_booking_gym' => $bookingId,
        ]);
    }

    public function test_returns_404_for_invalid_booking()
    {
        $this->actingAsKasir();

        $this->putJson('/api/presensiGym/99999')
             ->assertStatus(404);
    }
}