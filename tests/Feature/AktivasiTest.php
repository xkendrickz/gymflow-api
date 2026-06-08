<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

class AktivasiTest extends TestCase
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

    private function makeMember(): Member
    {
        return Member::create([
            'member_id'            => '25.01.1',
            'nama_member'          => 'Member Test',
            'alamat'               => 'Jl. Test',
            'tanggal_lahir'        => '1995-05-10',
            'tanggal_daftar'       => now()->toDateString(),
            'telepon'              => '08123456789',
            'email'                => 'member@test.com',
            'status'               => 0,
            'sisa_deposit_reguler' => 0,
            'sisa_deposit_paket'   => 0,
            'username'             => 'member01',
            'password'             => Hash::make('password'),
        ]);
    }

    public function test_can_create_aktivasi()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();

        $this->postJson('/api/aktivasi', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
        ])
        ->assertStatus(201)
        ->assertJsonPath('message', 'Berhasil Transaksi Aktivasi');
    }

    public function test_aktivasi_sets_member_status_to_active()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();

        $this->postJson('/api/aktivasi', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
        ]);

        $this->assertDatabaseHas('member', [
            'id_member' => $member->id_member,
            'status'    => 1,
        ]);
    }

    public function test_aktivasi_creates_masa_aktif_one_year_from_now()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();

        $this->postJson('/api/aktivasi', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
        ]);

        $aktivasi = DB::table('aktivasi')
            ->where('id_member', $member->id_member)
            ->first();

        $this->assertNotNull($aktivasi);
        $masaAktif = \Carbon\Carbon::parse($aktivasi->masa_aktif);
        $this->assertTrue($masaAktif->isAfter(now()->addMonths(11)));
    }
}