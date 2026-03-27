<?php

namespace App\Http\Controllers;

use App\Models\BookingGym;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingGymController extends Controller
{
    public function store(Request $request)
	{
		$storeData = $request->all();

		$validate = Validator::make($storeData, [
			'id_member' => 'required',
			'tanggal' => 'required',
			'slot_waktu' => 'required',
		]);

		if ($validate->fails()) {
			return response(['message' => $validate->errors()], 400);
		}

		$storeData['status'] = 0;

		$member = Member::find($storeData['id_member']);
		if (!$member || $member->status != 1) {
			return response(['message' => 'Member is not active'], 400);
		}

		$bookingsCount = BookingGym::where('tanggal', $storeData['tanggal'])
									->where('slot_waktu', $storeData['slot_waktu'])
									->count();
		if ($bookingsCount >= 10) {
			return response(['message' => 'Booking capacity reached limit'], 400);
		}

		$existingBooking = BookingGym::where('id_member', $storeData['id_member'])
									->where('tanggal', $storeData['tanggal'])
									->first();
		if ($existingBooking) {
			return response(['message' => 'Member has already made a booking for the selected date'], 400);
		}

		$bookingGym = BookingGym::create($storeData);

		return response([
			'message' => 'Add Booking Gym Success',
			'data' => $bookingGym
		], 200);
	}

	public function show($id_member)
	{
		$bookingGyms = BookingGym::where('id_member', $id_member)
			->where('status', 0)
			->get();

		if ($bookingGyms->isEmpty()) {
			return response([
				'message' => 'No Booking Gym Found',
				'data' => []
			], 404);
		}

		return response([
			'message' => 'Retrieve Booking Gym Success',
			'data' => $bookingGyms
		], 200);
	}

	public function destroy($id_member, $tanggal)
	{
		$bookingGym = BookingGym::where('id_member', $id_member)
			->where('tanggal', $tanggal)
			->first();

		if (is_null($bookingGym)) {
			return response([
				'message' => 'Booking Gym Not Found',
				'data' => null
			], 404);
		}

		$minimumDate = date('Y-m-d', strtotime('-1 day'));

		if ($tanggal <= $minimumDate) {
			if ($bookingGym->delete()) {
				return response([
					'message' => 'Cancel Booking Gym Success',
					'data' => $bookingGym
				], 200);
			} else {
				return response([
					'message' => 'Cancel Booking Gym Failed',
					'data' => null
				], 400);
			}
		} else {
			return response([
				'message' => 'Cancel is only allowed at least one day in advance',
				'data' => null
			], 400);
		}
	}

}
