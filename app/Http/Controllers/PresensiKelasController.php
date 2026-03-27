<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiKelasController extends Controller
{
    public function index()
    {
        $data = DB::table('presensi_kelas')
            ->join('booking_kelas', 'presensi_kelas.id_booking_kelas', '=', 'booking_kelas.id_booking_kelas')
            ->join('member', 'booking_kelas.id_member', '=', 'member.id_member')
            ->join('jadwal_harian', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->select(
                'presensi_kelas.id_presensi_kelas',
                'presensi_kelas.no_struk',
                'presensi_kelas.tanggal',
                'booking_kelas.jenis',
                'booking_kelas.status',
                'jadwal_harian.hari',
                'kelas.nama_kelas',
                'instruktur.nama_instruktur',
                'member.nama_member'
            )
            ->orderBy('jadwal_harian.hari', 'asc')
            ->get();

        return response()->json([
            'message' => 'Data Presensi Kelas Berhasil Ditampilkan!',
            'data'    => $data,
        ]);
    }

    public function show($id_instruktur)
    {
        $today = Carbon::today()->toDateString();

        $data = DB::table('booking_kelas')
            ->join('member', 'booking_kelas.id_member', '=', 'member.id_member')
            ->join('jadwal_harian', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->select(
                'kelas.nama_kelas',
                'instruktur.nama_instruktur',
                'member.nama_member',
                'booking_kelas.id_booking_kelas',
                'booking_kelas.jenis',
                'booking_kelas.status',
            )
            ->where('instruktur.id_instruktur', $id_instruktur)
            ->where('jadwal_harian.hari', $today)
            ->get();

        return response()->json([
            'message' => 'Data Presensi Kelas Berhasil Ditampilkan!',
            'data'    => $data,
        ]);
    }

    public function update($id)
    {
        DB::table('booking_kelas')
            ->where('id_booking_kelas', $id)
            ->update(['status' => 1]);

        $now      = Carbon::now();
        $no_struk = $now->format('y.m') . '.' . $id;

        DB::table('presensi_kelas')->insert([
            'id_booking_kelas' => $id,
            'no_struk'         => $no_struk,
            'tanggal'          => $now->format('Y-m-d H:i:s'),
        ]);

        return response()->json(['message' => 'Presensi Kelas berhasil dicatat.']);
    }

    public function cetakStruk($id)
    {
        $data = DB::table('presensi_kelas')
            ->join('booking_kelas', 'presensi_kelas.id_booking_kelas', '=', 'booking_kelas.id_booking_kelas')
            ->join('member', 'booking_kelas.id_member', '=', 'member.id_member')
            ->join('jadwal_harian', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->leftJoin('deposit_paket', 'member.id_member', '=', 'deposit_paket.id_member')
            ->select(
                'presensi_kelas.no_struk',
                DB::raw("DATE_FORMAT(presensi_kelas.tanggal, '%d/%m/%Y %H:%i') as tanggal"),
                'booking_kelas.jenis',
                'member.member_id',
                'member.nama_member',
                'kelas.nama_kelas',
                'instruktur.nama_instruktur',
                'kelas.tarif',
                'member.sisa_deposit_reguler',
                'member.sisa_deposit_paket',
                'deposit_paket.berlaku_sampai'
            )
            ->where('presensi_kelas.id_presensi_kelas', $id)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'message' => 'Data Struk Berhasil Ditampilkan!',
            'data'    => $data,
        ]);
    }
}