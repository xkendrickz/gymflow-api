<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DepositKelas;
use Carbon\Carbon;

class DepositKelasController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_member'  => 'required|exists:member,id_member',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'id_kelas'   => 'required|exists:kelas,id_kelas',
            'deposit'    => 'required|in:5,10',
        ]);

        $existing = DB::table('deposit_paket')
            ->where('id_member', $validated['id_member'])
            ->where('id_kelas', $validated['id_kelas'])
            ->where('jumlah_deposit_paket', '>', 0)
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Member masih memiliki deposit aktif untuk kelas ini.',
            ], 400);
        }

        $kelas  = DB::table('kelas')->where('id_kelas', $validated['id_kelas'])->firstOrFail();
        $harga  = $kelas->tarif * $validated['deposit'];
        $jumlah = $validated['deposit'] == 5 ? 6 : 13;

        $now         = Carbon::now();
        $no_struk    = $now->format('y') . $now->format('m') . $validated['id_kelas'];
        $berlakuSampai = $now->copy()->addMonth()->addDay()->toDateString();

        $depositKelas = DB::table('deposit_paket')->insertGetId([
            'id_member'           => $validated['id_member'],
            'id_pegawai'          => $validated['id_pegawai'],
            'id_kelas'            => $validated['id_kelas'],
            'no_struk'            => $no_struk,
            'tanggal'             => $now->toDateString(),
            'deposit'             => $validated['deposit'],
            'harga'               => $harga,
            'jumlah_deposit_paket'=> $jumlah,
            'berlaku_sampai'      => $berlakuSampai,
        ]);

        return response()->json([
            'message' => 'Deposit kelas berhasil disimpan.',
            'data'    => ['id_deposit_paket' => $depositKelas],
        ], 201);
    }

    public function show($id)
    {
        $data = DB::table('deposit_paket')
            ->join('member', 'deposit_paket.id_member', '=', 'member.id_member')
            ->join('pegawai', 'deposit_paket.id_pegawai', '=', 'pegawai.id_pegawai')
            ->join('kelas', 'deposit_paket.id_kelas', '=', 'kelas.id_kelas')
            ->select('deposit_paket.*', 'member.nama_member', 'member.member_id',
                'pegawai.nama_pegawai', 'kelas.nama_kelas')
            ->where('deposit_paket.id_deposit_paket', $id)
            ->first();

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $data]);
    }
}