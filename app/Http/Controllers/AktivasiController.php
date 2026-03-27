<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aktivasi;
use Illuminate\Support\Facades\DB;

class AktivasiController extends Controller
{
	/**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
	{
		$currentYear = date('y');
		$currentMonth = date('m');

		$latestAktivasi = DB::table('aktivasi')->latest('id_aktivasi')->first();
		$latestIdAktivasi = $latestAktivasi ? $latestAktivasi->id_aktivasi + 1 : 1;

		$idMember = $request->input('id_member');
		$idPegawai = $request->input('id_pegawai');

		$noStruk = $currentYear . '.' . $currentMonth . '.' . $latestIdAktivasi;

		$currentDateTime = date('Y-m-d H:i:s');

		$masaAktif = date('Y-m-d', strtotime('+1 year', strtotime($currentDateTime)));

		$aktivasi = DB::table('aktivasi')->insertGetId([
			'id_member' => $idMember,
			'id_pegawai' => $idPegawai,
			'no_struk' => $noStruk,
			'tanggal_aktivasi' => $currentDateTime,
			'harga' => 3000000,
			'masa_aktif' => $masaAktif
		]);

		DB::table('member')->where('id_member', $idMember)->update(['status' => true]);

		$response = [
			'status' => 201,
			'error' => false,
			'message' => 'Berhasil Transaksi Aktivasi',
			'data' => [
				'id_aktivasi' => $aktivasi
			]
		];

		return response()->json($response, 201);
	}

	public function show($id)
	{
		try {
			$aktivasi = Aktivasi::select('aktivasi.*', 'member.nama_member', 'member.member_id', 'pegawai.nama_pegawai', 'member.member_id')
				->join('member', 'aktivasi.id_member', '=', 'member.id_member')
				->join('pegawai', 'aktivasi.id_pegawai', '=', 'pegawai.id_pegawai')
				->findOrFail($id);
			return response()->json(['data' => $aktivasi], 200);
		} catch (\Throwable $th) {
			return response()->json(['message' => 'Aktivasi not found.'], 404);
		}
	}

}
