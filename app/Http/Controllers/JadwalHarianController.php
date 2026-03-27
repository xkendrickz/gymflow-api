<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalHarian;
use App\Models\JadwalUmum;
use Carbon\Carbon;

class JadwalHarianController extends Controller
{
    public function index()
    {
        $startDate = Carbon::today()->toDateString();
        $endDate   = Carbon::today()->addDays(7)->toDateString();

        $data = DB::table('jadwal_harian')
            ->select(
                'jadwal_harian.id_jadwal_harian',
                'jadwal_harian.hari',
                'jadwal_umum.jam',
                'kelas.nama_kelas',
                'instruktur.nama_instruktur',
                'kelas.tarif',
                DB::raw('IF(izin.konfirmasi = 1, izin.detail_izin, NULL) as status')
            )
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->leftJoin('izin', 'jadwal_harian.id_jadwal_harian', '=', 'izin.id_jadwal_harian')
            ->whereBetween('jadwal_harian.hari', [$startDate, $endDate])
            ->orderBy('jadwal_harian.hari', 'asc')
            ->orderBy('jadwal_umum.jam', 'asc')
            ->get();

        return response()->json([
            'status'    => 200,
            'totaldata' => $data->count(),
            'data'      => $data,
        ]);
    }

    public function store()
    {
        $today     = Carbon::today();
        $jadwalUmum = JadwalUmum::all();

        foreach ($jadwalUmum as $jadwal) {
            $dayMap = [
                'monday'    => Carbon::MONDAY,
                'tuesday'   => Carbon::TUESDAY,
                'wednesday' => Carbon::WEDNESDAY,
                'thursday'  => Carbon::THURSDAY,
                'friday'    => Carbon::FRIDAY,
                'saturday'  => Carbon::SATURDAY,
                'sunday'    => Carbon::SUNDAY,
            ];

            $targetDay  = $dayMap[$jadwal->hari] ?? null;
            if ($targetDay === null) continue;

            $nextDate = $today->copy()->next($targetDay);

            JadwalHarian::create([
                'id_jadwal_umum' => $jadwal->id_jadwal_umum,
                'hari'           => $nextDate->toDateString(),
            ]);
        }

        return response()->json(['message' => 'Jadwal Harian berhasil di-generate.'], 201);
    }

    public function show($id)
    {
        $data = DB::table('jadwal_harian')
            ->select('jadwal_harian.*', 'jadwal_umum.jam', 'kelas.nama_kelas', 'instruktur.nama_instruktur')
            ->join('jadwal_umum', 'jadwal_harian.id_jadwal_umum', '=', 'jadwal_umum.id_jadwal_umum')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->where('jadwal_harian.id_jadwal_harian', $id)
            ->get();

        return response()->json([
            'status' => 200,
            'data'   => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $jadwalHarian = JadwalHarian::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Libur,Digantikan',
        ]);

        $jadwalHarian->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status Jadwal Harian berhasil diupdate.',
            'data'    => $jadwalHarian,
        ]);
    }
}