<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class MemberTest extends TestCase
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

    private function makeMember(array $override = []): Member
    {
        return Member::create(array_merge([
            'member_id'            => '25.01.1',
            'nama_member'          => 'Member Test',
            'alamat'               => 'Jl. Test No. 1',
            'tanggal_lahir'        => '1995-05-10',
            'tanggal_daftar'       => now()->toDateString(),
            'telepon'              => '08123456789',
            'email'                => 'member@test.com',
            'status'               => 1,
            'sisa_deposit_reguler' => 0,
            'sisa_deposit_paket'   => 0,
            'username'             => 'member01',
            'password'             => Hash::make('password'),
        ], $override));
    }

    public function test_can_get_all_members()
    {
        $this->actingAsKasir();
        $this->makeMember();

        $this->getJson('/api/member')
             ->assertStatus(200)
             ->assertJsonStructure(['success', 'data']);
    }

    public function test_can_get_single_member()
    {
        $this->actingAsKasir();
        $member = $this->makeMember();

        $this->getJson("/api/member/{$member->id_member}")
             ->assertStatus(200)
             ->assertJsonStructure(['data']);
    }

    public function test_can_create_member()
    {
        $this->actingAsKasir();

        $this->postJson('/api/member', [
            'nama_member'   => 'Budi Santoso',
            'alamat'        => 'Jl. Merdeka No. 10',
            'tanggal_lahir' => '1995-05-10',
            'telepon'       => '08123456789',
            'email'         => 'budi@test.com',
            'username'      => 'budi01',
            'password'      => 'password123',
        ])
        ->assertStatus(201)
        ->assertJsonPath('message', 'Register Success');

        $this->assertDatabaseHas('member', ['username' => 'budi01']);
    }

    public function test_member_password_is_hashed()
    {
        $this->actingAsKasir();

        $this->postJson('/api/member', [
            'nama_member'   => 'Budi Santoso',
            'alamat'        => 'Jl. Merdeka No. 10',
            'tanggal_lahir' => '1995-05-10',
            'telepon'       => '08123456789',
            'email'         => 'budi@test.com',
            'username'      => 'budi01',
            'password'      => 'password123',
        ]);

        $member = Member::where('username', 'budi01')->first();
        $this->assertTrue(Hash::check('password123', $member->password));
        $this->assertNotEquals('password123', $member->password);
    }

    public function test_create_member_requires_all_fields()
    {
        $this->actingAsKasir();

        $this->postJson('/api/member', [])
             ->assertStatus(422)
             ->assertJsonValidationErrors([
                 'nama_member', 'alamat', 'tanggal_lahir',
                 'telepon', 'email', 'username', 'password',
             ]);
    }

    public function test_email_must_be_unique()
    {
        $this->actingAsKasir();
        $this->makeMember(['email' => 'duplicate@test.com']);

        $this->postJson('/api/member', [
            'nama_member'   => 'Other Member',
            'alamat'        => 'Jl. Test',
            'tanggal_lahir' => '1995-05-10',
            'telepon'       => '08199999999',
            'email'         => 'duplicate@test.com',
            'username'      => 'othermember',
            'password'      => 'password123',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }

    public function test_username_must_be_unique()
    {
        $this->actingAsKasir();
        $this->makeMember(['username' => 'taken01']);

        $this->postJson('/api/member', [
            'nama_member'   => 'Other Member',
            'alamat'        => 'Jl. Test',
            'tanggal_lahir' => '1995-05-10',
            'telepon'       => '08199999999',
            'email'         => 'other@test.com',
            'username'      => 'taken01',
            'password'      => 'password123',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['username']);
    }

    public function test_member_id_is_auto_generated()
    {
        $this->actingAsKasir();

        $this->postJson('/api/member', [
            'nama_member'   => 'Budi Santoso',
            'alamat'        => 'Jl. Test',
            'tanggal_lahir' => '1995-05-10',
            'telepon'       => '08123456789',
            'email'         => 'budi@test.com',
            'username'      => 'budi01',
            'password'      => 'password123',
        ]);

        $member = Member::where('username', 'budi01')->first();
        $this->assertNotNull($member->member_id);
    }

    public function test_new_member_status_is_inactive()
    {
        $this->actingAsKasir();

        $this->postJson('/api/member', [
            'nama_member'   => 'Budi Santoso',
            'alamat'        => 'Jl. Test',
            'tanggal_lahir' => '1995-05-10',
            'telepon'       => '08123456789',
            'email'         => 'budi@test.com',
            'username'      => 'budi01',
            'password'      => 'password123',
        ]);

        $this->assertDatabaseHas('member', [
            'username' => 'budi01',
            'status'   => 0,
        ]);
    }
}