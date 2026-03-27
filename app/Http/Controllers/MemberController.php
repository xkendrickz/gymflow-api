<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use Carbon\Carbon;

class MemberController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => Member::all(),
        ]);
    }

    public function show($id)
    {
        $member = Member::findOrFail($id);
        return response()->json(['data' => $member]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_member'   => 'required|string|max:255',
            'alamat'        => 'required|string',
            'tanggal_lahir' => 'required|date',
            'telepon'       => 'required|string|max:20',
            'email'         => 'required|email|unique:member,email',
            'username'      => 'required|string|unique:member,username',
            'password'      => 'required|string|min:6',
        ]);

        $now         = Carbon::now();
        $latestId    = Member::max('id_member') ?? 0;
        $memberId    = $now->format('y') . '.' . $now->format('m') . '.' . ($latestId + 1);

        $member = Member::create([
            'member_id'            => $memberId,
            'nama_member'          => $validated['nama_member'],
            'alamat'               => $validated['alamat'],
            'tanggal_lahir'        => $validated['tanggal_lahir'],
            'telepon'              => $validated['telepon'],
            'email'                => $validated['email'],
            'username'             => $validated['username'],
            'password'             => Hash::make($validated['password']),
            'tanggal_daftar'       => $now,
            'status'               => 0,
            'sisa_deposit_reguler' => 0,
            'sisa_deposit_paket'   => 0,
        ]);

        return response()->json([
            'message' => 'Register Success',
            'data'    => $member,
        ], 201);
    }

    public function profileMember($id)
    {
        $member = Member::findOrFail($id);

        $data = [
            'nama_member'          => $member->nama_member,
            'alamat'               => $member->alamat,
            'tanggal_lahir'        => $member->tanggal_lahir,
            'telepon'              => $member->telepon,
            'email'                => $member->email,
            'sisa_deposit_reguler' => $member->sisa_deposit_reguler,
            'sisa_deposit_paket'   => $member->sisa_deposit_paket,
            'masa_aktif'           => null,
            'nama_kelas'           => null,
        ];

        $aktivasi = Aktivasi::where('id_member', $id)->latest('tanggal_aktivasi')->first();
        if ($aktivasi) {
            $data['masa_aktif'] = $aktivasi->masa_aktif;
        }

        $depositPaket = DepositKelas::where('id_member', $id)
            ->join('kelas', 'deposit_paket.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas')
            ->latest('deposit_paket.tanggal')
            ->first();

        if ($depositPaket) {
            $data['nama_kelas'] = $depositPaket->nama_kelas;
        }

        return response()->json([
            'message' => 'Retrieve Member Success',
            'data'    => $data,
        ]);
    }
}