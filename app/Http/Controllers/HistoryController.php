<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function historyMemberTransaksi($id)
    {
        $aktivasi = DB::table('aktivasi')
            ->select(DB::raw("'Aktivasi' as nama_aktivitas"), 'tanggal_aktivasi as tanggal',
                DB::raw("CONCAT('Rp.', harga) as harga"), DB::raw('NULL as kelas'))
            ->where('id_member', $id);

        $depositKelas = DB::table('deposit_paket')
            ->join('kelas', 'deposit_paket.id_kelas', '=', 'kelas.id_kelas')
            ->select(DB::raw("'Deposit Paket' as nama_aktivitas"), 'deposit_paket.tanggal',
                DB::raw("CONCAT(deposit_paket.deposit, '(Rp.', deposit_paket.harga, ')') as harga"),
                'kelas.nama_kelas as kelas')
            ->where('deposit_paket.id_member', $id);

        $depositReguler = DB::table('deposit_reguler')
            ->select(DB::raw("'Deposit Reguler' as nama_aktivitas"), 'tanggal',
                DB::raw("CONCAT('Rp.', deposit) as harga"), DB::raw('NULL as kelas'))
            ->where('id_member', $id);

        $data = $aktivasi
            ->union($depositKelas)
            ->union($depositReguler)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data'   => $data,
        ]);
    }

    public function historyMemberPresensi($id)
    {
        $gyms = DB::table('booking_gym')
            ->select(
                DB::raw("'Gym' as nama_aktivitas"),
                'tanggal',
                'slot_waktu as jenis',
                DB::raw("IF(status = 1, 'Hadir', 'Tidak Hadir') as status"),
                DB::raw('NULL as kelas')
            )
            ->where('id_member', $id);

        $kelass = DB::table('booking_kelas')
            ->join('jadwal_harian', 'booking_kelas.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->select(
                DB::raw("'Kelas' as nama_aktivitas"),
                'jadwal_harian.hari as tanggal',
                'booking_kelas.jenis',
                DB::raw("IF(booking_kelas.status = 1, 'Hadir', 'Tidak Hadir') as status"),
                'kelas.nama_kelas as kelas'
            )
            ->where('booking_kelas.id_member', $id);

        $data = $gyms->union($kelass)->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'status' => 200,
            'data'   => $data,
        ]);
    }

    public function historyInstruktur($id)
    {
        $presensis = DB::table('presensi_instruktur')
            ->join('jadwal_harian', 'presensi_instruktur.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas', 'jadwal_harian.hari',
                'presensi_instruktur.mulai_kelas', 'presensi_instruktur.selesai_kelas',
                DB::raw('NULL as izin'))
            ->where('jadwal_umum.id_instruktur', $id);

        $izins = DB::table('izin')
            ->join('jadwal_harian', 'izin.id_jadwal_harian', '=', 'jadwal_harian.id_jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas', 'jadwal_harian.hari',
                DB::raw('NULL as mulai_kelas'), DB::raw('NULL as selesai_kelas'),
                'izin.detail_izin as izin')
            ->where('jadwal_umum.id_instruktur', $id);

        $data = $presensis->union($izins)->orderBy('hari', 'desc')->get();

        return response()->json([
            'status' => 200,
            'data'   => $data,
        ]);
    }
}