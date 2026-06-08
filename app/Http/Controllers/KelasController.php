<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Kelas::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tarif'      => 'required|numeric|min:0',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tarif'      => $request->tarif,
        ]);

        return response()->json([
            'message' => 'Kelas berhasil ditambahkan.',
            'data'    => $kelas,
        ], 201);
    }

    public function show($id)
    {
        return response()->json(['data' => Kelas::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tarif'      => 'required|numeric|min:0',
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tarif'      => $request->tarif,
        ]);

        return response()->json([
            'message' => 'Kelas berhasil diupdate.',
            'data'    => $kelas,
        ]);
    }

    public function destroy($id)
    {
        Kelas::findOrFail($id)->delete();
        return response()->json(['message' => 'Kelas berhasil dihapus.']);
    }
}