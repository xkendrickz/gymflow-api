<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Member;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

class DepositKelasTest extends TestCase
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
            'status'               => 1,
            'sisa_deposit_reguler' => 0,
            'sisa_deposit_paket'   => 0,
            'username'             => 'member01',
            'password'             => Hash::make('password'),
        ]);
    }

    public function test_can_create_deposit_kelas()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();
        $kelas  = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        $this->postJson('/api/depositKelas', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'id_kelas'   => $kelas->id_kelas,
            'deposit'    => 5,
        ])
        ->assertStatus(201)
        ->assertJsonPath('message', 'Deposit kelas berhasil disimpan.');
    }

    public function test_deposit_5_gives_6_sessions()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();
        $kelas  = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        $this->postJson('/api/depositKelas', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'id_kelas'   => $kelas->id_kelas,
            'deposit'    => 5,
        ]);

        $this->assertDatabaseHas('deposit_paket', [
            'id_member'            => $member->id_member,
            'jumlah_deposit_paket' => 6,
        ]);
    }

    public function test_deposit_10_gives_13_sessions()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();
        $kelas  = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        $this->postJson('/api/depositKelas', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'id_kelas'   => $kelas->id_kelas,
            'deposit'    => 10,
        ]);

        $this->assertDatabaseHas('deposit_paket', [
            'id_member'            => $member->id_member,
            'jumlah_deposit_paket' => 13,
        ]);
    }

    public function test_rejects_duplicate_active_deposit()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();
        $kelas  = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        DB::table('deposit_paket')->insert([
            'id_member'            => $member->id_member,
            'id_pegawai'           => $kasir->id_pegawai,
            'id_kelas'             => $kelas->id_kelas,
            'no_struk'             => '2501001',
            'tanggal'              => now()->toDateString(),
            'deposit'              => 5,
            'harga'                => 250000,
            'jumlah_deposit_paket' => 6,
            'berlaku_sampai'       => now()->addMonth()->toDateString(),
        ]);

        $this->postJson('/api/depositKelas', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'id_kelas'   => $kelas->id_kelas,
            'deposit'    => 5,
        ])
        ->assertStatus(400)
        ->assertJsonPath('message', 'Member masih memiliki deposit aktif untuk kelas ini.');
    }

    public function test_deposit_must_be_5_or_10()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();
        $kelas  = Kelas::create(['nama_kelas' => 'Yoga', 'tarif' => 50000]);

        $this->postJson('/api/depositKelas', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'id_kelas'   => $kelas->id_kelas,
            'deposit'    => 7,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['deposit']);
    }

    public function test_deposit_kelas_requires_all_fields()
    {
        $this->actingAsKasir();

        $this->postJson('/api/depositKelas', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors([
                 'id_member', 'id_pegawai', 'id_kelas', 'deposit'
             ]);
    }
}