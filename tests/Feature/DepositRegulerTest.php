<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class DepositRegulerTest extends TestCase
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

    private function makeMember(int $sisa = 0): Member
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
            'sisa_deposit_reguler' => $sisa,
            'sisa_deposit_paket'   => 0,
            'username'             => 'member01',
            'password'             => Hash::make('password'),
        ]);
    }

    public function test_can_create_deposit_reguler()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();

        $this->postJson('/api/depositReguler', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'deposit'    => 100000,
        ])
        ->assertStatus(201)
        ->assertJsonPath('message', 'Deposit reguler berhasil disimpan.');
    }

    public function test_deposit_updates_member_balance()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember(sisa: 200000);

        $this->postJson('/api/depositReguler', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'deposit'    => 100000,
        ]);

        $this->assertDatabaseHas('member', [
            'id_member'            => $member->id_member,
            'sisa_deposit_reguler' => 300000,
        ]);
    }

    public function test_deposit_gives_bonus_when_eligible()
    {
        $kasir  = $this->actingAsKasir();
        // sisa >= 500000 AND deposit >= 3000000 triggers bonus
        $member = $this->makeMember(sisa: 500000);

        $this->postJson('/api/depositReguler', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'deposit'    => 3000000,
        ]);

        // bonus = floor(3000000/3000000) * 300000 = 300000
        // total = 500000 + 3000000 + 300000 = 3800000
        $this->assertDatabaseHas('member', [
            'id_member'            => $member->id_member,
            'sisa_deposit_reguler' => 3800000,
        ]);
    }

    public function test_deposit_requires_all_fields()
    {
        $this->actingAsKasir();

        $this->postJson('/api/depositReguler', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['id_member', 'id_pegawai', 'deposit']);
    }

    public function test_deposit_must_be_positive()
    {
        $kasir  = $this->actingAsKasir();
        $member = $this->makeMember();

        $this->postJson('/api/depositReguler', [
            'id_member'  => $member->id_member,
            'id_pegawai' => $kasir->id_pegawai,
            'deposit'    => 0,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['deposit']);
    }
}