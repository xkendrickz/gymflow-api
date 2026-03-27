<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PresensiInstruktur;
use Carbon\Carbon;

class PresensiInstrukturController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $data = DB::table('instruktur')
            ->select(
                'instruktur.id_instruktur',
                'instruktur.nama_instruktur',
                'kelas.id_kelas',
                'kelas.nama_kelas',
                'jadwal_harian.id_jadwal_harian'
            )
            ->join('jadwal_umum', 'instruktur.id_instruktur', '=', 'jadwal_umum.id_instruktur')
            ->join('jadwal_harian', 'jadwal_umum.id_jadwal_umum', '=', 'jadwal_harian.id_jadwal_umum')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->where('jadwal_harian.hari', $today)
            ->get();

        return response()->json([
            'message' => 'Data Kelas Berhasil Ditampilkan!',
            'data'    => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jadwal_harian' => 'required|exists:jadwal_harian,id_jadwal_harian',
            'mulai_kelas'      => 'required|date_format:H:i',
            'selesai_kelas'    => 'required|date_format:H:i|after:mulai_kelas',
        ]);

        $presensi = PresensiInstruktur::create($validated);

        return response()->json([
            'message' => 'Presensi Instruktur berhasil dicatat.',
            'data'    => $presensi,
        ], 201);
    }
}