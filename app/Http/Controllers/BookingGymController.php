<?php
namespace App\Http\Controllers;
use App\Models\BookingGym;
use App\Models\Member;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingGymController extends Controller
{
    public function store(Request $request)
    {
        // use $request->validate() for proper 422 instead of manual Validator
        $request->validate([
            'id_member'  => 'required|exists:member,id_member',
            'tanggal'    => 'required|date',
            'slot_waktu' => 'required|string',
        ]);

        $member = Member::find($request->id_member);
        if (!$member || $member->status != 1) {
            return response(['message' => 'Member is not active'], 400);
        }

        $bookingsCount = BookingGym::where('tanggal', $request->tanggal)
                                    ->where('slot_waktu', $request->slot_waktu)
                                    ->count();
        if ($bookingsCount >= 10) {
            return response(['message' => 'Booking capacity reached limit'], 400);
        }

        $existingBooking = BookingGym::where('id_member', $request->id_member)
                                      ->where('tanggal', $request->tanggal)
                                      ->first();
        if ($existingBooking) {
            return response(['message' => 'Member has already made a booking for this date'], 400);
        }

        $bookingGym = BookingGym::create([
            'id_member'  => $request->id_member,
            'tanggal'    => $request->tanggal,
            'slot_waktu' => $request->slot_waktu,
            'status'     => 0,
        ]);

        return response([
            'message' => 'Add Booking Gym Success',
            'data'    => $bookingGym
        ], 201); // fix: was 200, should be 201 for resource creation
    }

    public function show($id_member)
    {
        $bookingGyms = BookingGym::where('id_member', $id_member)
            ->where('status', 0)
            ->get();

        return response([
            'message' => $bookingGyms->isEmpty() ? 'No Booking Gym Found' : 'Retrieve Booking Gym Success',
            'data'    => $bookingGyms
        ], 200);
    }

    public function destroy($id_member, $tanggal)
    {
        $bookingGym = BookingGym::where('id_member', $id_member)
            ->where('tanggal', $tanggal)
            ->first();

        if (is_null($bookingGym)) {
            return response(['message' => 'Booking Gym Not Found', 'data' => null], 404);
        }

        // fix: logic was inverted - should allow cancel for FUTURE bookings (tomorrow+)
        $minimumDate = Carbon::tomorrow()->toDateString();
        if ($tanggal >= $minimumDate) {
            $bookingGym->delete();
            return response(['message' => 'Cancel Booking Gym Success', 'data' => $bookingGym], 200);
        }

        return response([
            'message' => 'Cancel is only allowed at least one day in advance',
            'data'    => null
        ], 400);
    }
}