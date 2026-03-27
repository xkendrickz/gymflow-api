<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::where('id_role', 3)->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Pegawai',
            'data'    => $pegawai,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_role'       => 'required|integer',
            'nama_pegawai'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'username'      => 'required|string|unique:pegawai,username',
            'password'      => 'required|string|min:6',
        ]);

        $pegawai = Pegawai::create([
            'id_role'       => $validated['id_role'],
            'nama_pegawai'  => $validated['nama_pegawai'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'username'      => $validated['username'],
            'password'      => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Pegawai Berhasil Ditambahkan!',
            'data'    => $pegawai,
        ], 201);
    }

    public function show($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Data Pegawai Berhasil Ditampilkan!',
            'data'    => $pegawai,
        ]);
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Pegawai Berhasil Dihapus!',
        ]);
    }

    public function profilePegawai($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return response()->json([
            'message' => 'Retrieve Pegawai Success',
            'data'    => [
                'nama_pegawai'  => $pegawai->nama_pegawai,
                'tanggal_lahir' => $pegawai->tanggal_lahir,
            ],
        ]);
    }
}