<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalUmum;

class JadwalUmumController extends Controller
{
    public function index()
    {
        $data = DB::table('jadwal_umum')
            ->select('jadwal_umum.*', 'instruktur.nama_instruktur', 'kelas.nama_kelas', 'kelas.tarif')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->orderBy('jadwal_umum.hari', 'asc')
            ->get();

        return response()->json([
            'status'    => 200,
            'totaldata' => $data->count(),
            'data'      => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari'          => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'id_kelas'      => 'required|exists:kelas,id_kelas',
            'id_instruktur' => 'required|exists:instruktur,id_instruktur',
            'jam'           => 'required|date_format:H:i',
        ]);

        $jadwal = JadwalUmum::create($validated);

        return response()->json([
            'message' => 'Jadwal Umum berhasil ditambahkan.',
            'data'    => $jadwal,
        ], 201);
    }

    public function show($id)
    {
        $jadwal = DB::table('jadwal_umum')
            ->select('jadwal_umum.*', 'instruktur.nama_instruktur', 'kelas.nama_kelas')
            ->join('instruktur', 'jadwal_umum.id_instruktur', '=', 'instruktur.id_instruktur')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->where('jadwal_umum.id_jadwal_umum', $id)
            ->firstOrFail();

        return response()->json(['data' => $jadwal]);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalUmum::findOrFail($id);

        $validated = $request->validate([
            'hari' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'jam'  => 'required|date_format:H:i',
        ]);

        $jadwal->update($validated);

        return response()->json([
            'message' => 'Jadwal Umum berhasil diubah.',
            'data'    => $jadwal,
        ]);
    }

    public function destroy($id)
    {
        $jadwal = JadwalUmum::findOrFail($id);
        $jadwal->delete();

        return response()->json(['message' => 'Jadwal Umum berhasil dihapus.']);
    }
}