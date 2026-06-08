<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $data = Pegawai::select('id_pegawai', 'id_role', 'nama_pegawai', 'tanggal_lahir', 'username')
            ->orderBy('id_role')
            ->orderBy('nama_pegawai')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai'  => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'username'      => 'required|string|unique:pegawai,username',
            'password'      => 'required|string|min:6',
            'id_role'       => 'required|in:1,3',
        ]);

        $pegawai = Pegawai::create([
            'id_role'       => $request->id_role,
            'nama_pegawai'  => $request->nama_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'username'      => $request->username,
            'password'      => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Akun berhasil ditambahkan.',
            'data'    => $pegawai,
        ], 201);
    }

    public function show($id)
    {
        $pegawai = Pegawai::select('id_pegawai', 'id_role', 'nama_pegawai', 'tanggal_lahir', 'username')
            ->findOrFail($id);
        return response()->json(['data' => $pegawai]);
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama_pegawai'  => 'required|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'username'      => 'required|string|unique:pegawai,username,' . $id . ',id_pegawai',
            'password'      => 'nullable|string|min:6',
            'id_role'       => 'required|in:1,3',
        ]);

        $data = [
            'id_role'       => $request->id_role,
            'nama_pegawai'  => $request->nama_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'username'      => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pegawai->update($data);

        return response()->json([
            'message' => 'Akun berhasil diupdate.',
            'data'    => $pegawai,
        ]);
    }

    public function destroy($id)
    {
        Pegawai::findOrFail($id)->delete();
        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }

    public function profilePegawai($id)
    {
        $pegawai = Pegawai::select('id_pegawai', 'id_role', 'nama_pegawai', 'tanggal_lahir', 'username')
            ->findOrFail($id);
        return response()->json(['data' => $pegawai]);
    }
}