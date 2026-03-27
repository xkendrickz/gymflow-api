<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Instruktur;

class InstrukturController extends Controller
{
    public function index()
    {
        $instruktur = Instruktur::latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Instruktur',
            'data'    => $instruktur,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_instruktur' => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'username'        => 'required|string|unique:instruktur,username',
            'password'        => 'required|string|min:6',
        ]);

        $instruktur = Instruktur::create([
            'nama_instruktur'  => $validated['nama_instruktur'],
            'tanggal_lahir'    => $validated['tanggal_lahir'],
            'username'         => $validated['username'],
            'password'         => Hash::make($validated['password']),
            'jumlah_hadir'     => 0,
            'jumlah_libur'     => 0,
            'waktu_terlambat'  => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Instruktur Berhasil Ditambahkan!',
            'data'    => $instruktur,
        ], 201);
    }

    public function show($id)
    {
        $instruktur = Instruktur::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Data Instruktur Berhasil Ditampilkan!',
            'data'    => $instruktur,
        ]);
    }

    public function update(Request $request, $id)
    {
        $instruktur = Instruktur::findOrFail($id);

        $validated = $request->validate([
            'nama_instruktur' => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'username'        => 'required|string|unique:instruktur,username,' . $id . ',id_instruktur',
            'password'        => 'nullable|string|min:6',
        ]);

        $instruktur->nama_instruktur = $validated['nama_instruktur'];
        $instruktur->tanggal_lahir   = $validated['tanggal_lahir'];
        $instruktur->username        = $validated['username'];

        if (!empty($validated['password'])) {
            $instruktur->password = Hash::make($validated['password']);
        }

        $instruktur->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Instruktur Berhasil Diubah!',
            'data'    => $instruktur,
        ]);
    }

    public function destroy($id)
    {
        $instruktur = Instruktur::findOrFail($id);
        $instruktur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Instruktur Berhasil Dihapus!',
        ]);
    }

    public function profileInstruktur($id)
    {
        $instruktur = Instruktur::findOrFail($id);

        return response()->json([
            'message' => 'Retrieve Instruktur Success',
            'data'    => [
                'nama_instruktur' => $instruktur->nama_instruktur,
                'tanggal_lahir'   => $instruktur->tanggal_lahir,
                'waktu_terlambat' => $instruktur->waktu_terlambat,
            ],
        ]);
    }
}