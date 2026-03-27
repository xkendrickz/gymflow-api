<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiGymController extends Controller
{
    public function index()
    {
        $data = DB::table('booking_gym')
            ->join('member', 'booking_gym.id_member', '=', 'member.id_member')
            ->select(
                'booking_gym.id_booking_gym',
                'member.nama_member',
                'booking_gym.tanggal',
                'booking_gym.slot_waktu',
                'booking_gym.status'
            )
            ->orderBy('booking_gym.tanggal', 'asc')
            ->get();

        return response()->json([
            'message' => 'Data Presensi Gym Berhasil Ditampilkan!',
            'data'    => $data,
        ]);
    }

    public function update($id)
    {
        $booking = DB::table('booking_gym')->where('id_booking_gym', $id)->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan.'], 404);
        }

        DB::table('booking_gym')
            ->where('id_booking_gym', $id)
            ->update(['status' => 1]);

        $tanggal  = Carbon::parse($booking->tanggal);
        $no_struk = $tanggal->format('y.m') . '.' . $id;
        $now      = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('presensi_gym')->insert([
            'id_booking_gym' => $id,
            'no_struk'       => $no_struk,
            'tanggal'        => $now,
        ]);

        $idPresensiGym = DB::getPdo()->lastInsertId();

        return response()->json([
            'message' => 'Presensi Gym berhasil dicatat.',
            'data'    => [
                'id_presensi_gym' => $idPresensiGym,
                'no_struk'        => $no_struk,
                'tanggal'         => $now,
            ],
        ]);
    }

    public function show($id)
    {
        $data = DB::table('presensi_gym')
            ->join('booking_gym', 'presensi_gym.id_booking_gym', '=', 'booking_gym.id_booking_gym')
            ->join('member', 'booking_gym.id_member', '=', 'member.id_member')
            ->select(
                DB::raw("DATE_FORMAT(presensi_gym.tanggal, '%d/%m/%Y %H:%i') AS tanggal"),
                'presensi_gym.no_struk',
                'member.member_id',
                'member.nama_member',
                'booking_gym.slot_waktu'
            )
            ->where('presensi_gym.id_presensi_gym', $id)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $data]);
    }
}