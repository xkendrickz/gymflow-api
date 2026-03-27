<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use App\Models\DepositReguler;
use App\Models\Instruktur;
use App\Models\BookingGym;
use App\Models\JadwalHarian;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function dropdownPendapatan()
    {
        $years = collect()
            ->merge(Aktivasi::pluck('tanggal_aktivasi')->map(fn ($t) => Carbon::parse($t)->year))
            ->merge(DepositReguler::pluck('tanggal')->map(fn ($t) => Carbon::parse($t)->year))
            ->merge(DepositKelas::pluck('tanggal')->map(fn ($t) => Carbon::parse($t)->year))
            ->unique()
            ->sort()
            ->values();

        return response()->json(['data' => $years]);
    }

    public function laporanPendapatan($tahun)
    {
        $monthlyData  = [];
        $totalTahunan = 0;

        for ($month = 1; $month <= 12; $month++) {
            $totalAktivasi = Aktivasi::whereYear('tanggal_aktivasi', $tahun)
                ->whereMonth('tanggal_aktivasi', $month)->sum('harga');

            $totalDepositPaket = DepositKelas::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $month)->sum('harga');

            $totalDepositReguler = DepositReguler::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $month)->sum('deposit');

            $totalDeposit  = $totalDepositPaket + $totalDepositReguler;
            $totalBulanan  = $totalAktivasi + $totalDeposit;
            $totalTahunan += $totalBulanan;

            $monthlyData[] = [
                'nama_bulan'      => Carbon::createFromDate($tahun, $month, 1)->format('F'),
                'total_aktivasi'  => $totalAktivasi,
                'total_deposit'   => $totalDeposit,
                'total_bulanan'   => $totalBulanan,
            ];
        }

        return response()->json([
            'data'          => $monthlyData,
            'total_tahunan' => $totalTahunan,
            'tahun'         => $tahun,
            'tanggal'       => now()->format('d F Y'),
        ]);
    }

    public function dropdownAktivitasGym()
    {
        return response()->json([
            'data' => [
                'months' => BookingGym::selectRaw('MONTH(tanggal) as month')->distinct()->orderBy('month')->get(),
                'years'  => BookingGym::selectRaw('YEAR(tanggal) as year')->distinct()->orderBy('year')->get(),
            ],
        ]);
    }

    public function laporanAktivitasGym($bulan, $tahun)
    {
        $data = BookingGym::select(
                DB::raw("DATE_FORMAT(tanggal, '%e %M %Y') as tanggal"),
                DB::raw('COUNT(id_member) as jumlah_member')
            )
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('tanggal')
            ->get();

        return response()->json([
            'data'    => $data,
            'total'   => $data->sum('jumlah_member'),
            'bulan'   => Carbon::createFromDate($tahun, $bulan, 1)->format('F'),
            'tahun'   => $tahun,
            'tanggal' => now()->format('d F Y'),
        ]);
    }

    public function dropdownAktivitasKelas()
    {
        return response()->json([
            'data' => [
                'months' => JadwalHarian::selectRaw('MONTH(hari) as month')->distinct()->orderBy('month')->get(),
                'years'  => JadwalHarian::selectRaw('YEAR(hari) as year')->distinct()->orderBy('year')->get(),
            ],
        ]);
    }

    public function laporanAktivitasKelas($bulan, $tahun)
    {
        $data = JadwalHarian::select('kelas.nama_kelas', 'instruktur.nama_instruktur')
            ->selectRaw('COUNT(booking_kelas.id_booking_kelas) as total_peserta')
            ->selectRaw('COUNT(izin.id_izin) as total_libur')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->leftJoin('booking_kelas', 'jadwal_harian.id_jadwal_harian', '=', 'booking_kelas.id_jadwal_harian')
            ->leftJoin('izin', 'jadwal_harian.id_jadwal_harian', '=', 'izin.id_jadwal_harian')
            ->whereYear('jadwal_harian.hari', $tahun)
            ->whereMonth('jadwal_harian.hari', $bulan)
            ->groupBy('kelas.nama_kelas', 'instruktur.nama_instruktur')
            ->orderBy('kelas.nama_kelas')
            ->get();

        return response()->json([
            'data'    => $data,
            'bulan'   => Carbon::createFromDate($tahun, $bulan, 1)->format('F'),
            'tahun'   => $tahun,
            'tanggal' => now()->format('d F Y'),
        ]);
    }

    public function laporanKinerjaInstruktur($bulan, $tahun)
    {
        $data = Instruktur::select('instruktur.nama_instruktur')
            ->selectRaw('COUNT(DISTINCT presensi_instruktur.id_presensi_instruktur) as jumlah_hadir')
            ->selectRaw('COUNT(DISTINCT izin.id_izin) as jumlah_libur')
            ->selectRaw('SUM(instruktur.waktu_terlambat) as waktu_terlambat')
            ->leftJoin('jadwal_umum', 'instruktur.id_instruktur', '=', 'jadwal_umum.id_instruktur')
            ->leftJoin('jadwal_harian', 'jadwal_umum.id_jadwal_umum', '=', 'jadwal_harian.id_jadwal_umum')
            ->leftJoin('presensi_instruktur', 'jadwal_harian.id_jadwal_harian', '=', 'presensi_instruktur.id_jadwal_harian')
            ->leftJoin('izin', 'jadwal_harian.id_jadwal_harian', '=', 'izin.id_jadwal_harian')
            ->whereMonth('jadwal_harian.hari', $bulan)
            ->whereYear('jadwal_harian.hari', $tahun)
            ->groupBy('instruktur.id_instruktur', 'instruktur.nama_instruktur')
            ->get();

        return response()->json([
            'data'    => $data,
            'bulan'   => Carbon::createFromDate($tahun, $bulan, 1)->format('F'),
            'tahun'   => $tahun,
            'tanggal' => now()->format('d F Y'),
        ]);
    }

    public function resetMember()
    {
        // Delegated to ResetController — not in LaporanController
    }
}