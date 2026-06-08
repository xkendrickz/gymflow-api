<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Izin;

class IzinController extends Controller
{
    public function index()
    {
        $izin = Izin::select('izin.*', 'instruktur.nama_instruktur')
            ->join('instruktur', 'izin.id_instruktur', '=', 'instruktur.id_instruktur')
            ->orderBy('izin.tanggal_izin', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'data'    => $izin,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_instruktur'    => 'required|exists:instruktur,id_instruktur',
            'id_jadwal_harian' => 'required|exists:jadwal_harian,id_jadwal_harian',
            'detail_izin'      => 'required|string|max:500',
            'tanggal_izin'     => 'required|date',
        ]);

        $izin = Izin::create([
            'id_instruktur'    => $request->id_instruktur,
            'id_jadwal_harian' => $request->id_jadwal_harian,
            'detail_izin'      => $request->detail_izin,
            'tanggal_izin'     => $request->tanggal_izin,
            'konfirmasi'       => 0,
        ]);

        return response()->json([
            'message' => 'Izin berhasil diajukan.',
            'data'    => $izin,
        ], 201);
    }

    public function update($id)
    {
        $izin = Izin::findOrFail($id);
        $izin->timestamps = false;
        $izin->konfirmasi = 1;
        $izin->save();
        return response()->json(['message' => 'Izin berhasil dikonfirmasi.']);
    }
}