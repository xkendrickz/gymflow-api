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

class BookingGymTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsMember(): Member
    {
        $member = Member::factory()->create();
        Sanctum::actingAs($member);
        return $member;
    }

    public function test_member_can_create_booking()
    {
        $member = $this->actingAsMember();

        $this->postJson('/api/bookingGym', [
            'id_member'  => $member->id_member,
            'tanggal'    => Carbon::tomorrow()->toDateString(),
            'slot_waktu' => '07:00:00',
        ])
        ->assertStatus(201);

        $this->assertDatabaseHas('booking_gym', [
            'id_member'  => $member->id_member,
            'slot_waktu' => '07:00:00',
        ]);
    }

    public function test_member_can_get_their_bookings()
    {
        $member = $this->actingAsMember();
        DB::table('booking_gym')->insert([
            'id_member'  => $member->id_member,
            'tanggal'    => Carbon::tomorrow()->toDateString(),
            'slot_waktu' => '07:00:00',
            'status'     => 0,
        ]);

        $this->getJson("/api/bookingGym/{$member->id_member}")
             ->assertStatus(200)
             ->assertJsonStructure(['data'])
             ->assertJsonCount(1, 'data');
    }

    public function test_member_can_delete_booking()
    {
        $member  = $this->actingAsMember();
        $tanggal = Carbon::tomorrow()->toDateString();
        DB::table('booking_gym')->insert([
            'id_member'  => $member->id_member,
            'tanggal'    => $tanggal,
            'slot_waktu' => '07:00:00',
            'status'     => 0,
        ]);

        $this->deleteJson("/api/bookingGym/{$member->id_member}/{$tanggal}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('booking_gym', [
            'id_member' => $member->id_member,
            'tanggal'   => $tanggal,
        ]);
    }

    public function test_booking_requires_tanggal_and_slot()
    {
        $member = $this->actingAsMember();

        $this->postJson('/api/bookingGym', [
            'id_member' => $member->id_member,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['tanggal', 'slot_waktu']);
    }
}