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

    public function update($id)
    {
        $izin = Izin::findOrFail($id);
        $izin->timestamps = false;
        $izin->konfirmasi = 1;
        $izin->save();

        return response()->json(['message' => 'Izin berhasil dikonfirmasi.']);
    }
}