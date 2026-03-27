<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Instruktur;
use App\Models\Aktivasi;
use App\Models\DepositKelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ResetController extends Controller
{
    public function indexAktivasi()
    {
        $today = Carbon::today();

        $data = Member::join('aktivasi', 'member.id_member', '=', 'aktivasi.id_member')
            ->where('member.status', 1)
            ->whereDate('aktivasi.masa_aktif', '<=', $today)
            ->select('member.nama_member', 'aktivasi.masa_aktif')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function indexDeposit()
    {
        $today = Carbon::today();

        $data = Member::join('deposit_paket', 'member.id_member', '=', 'deposit_paket.id_member')
            ->where('member.sisa_deposit_paket', '>', 0)
            ->whereDate('deposit_paket.berlaku_sampai', '<=', $today)
            ->select('member.nama_member', 'deposit_paket.berlaku_sampai')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function resetMember()
    {
        $today = Carbon::today()->toDateString();

        Member::where('status', 1)
            ->whereHas('aktivasi', fn ($q) => $q->where('masa_aktif', '<=', $today))
            ->each(function (Member $member) {
                $member->timestamps = false;
                $member->status = 0;
                $member->save();
            });

        Member::where('sisa_deposit_paket', '>', 0)
            ->whereHas('depositPaket', fn ($q) => $q->where('berlaku_sampai', '<=', now()))
            ->each(function (Member $member) {
                $member->timestamps = false;
                $member->sisa_deposit_paket = 0;
                $member->save();
            });

        return response()->json(['message' => 'Member berhasil di-reset.']);
    }

    public function resetInstruktur()
    {
        Instruktur::query()->update(['waktu_terlambat' => 0]);

        return response()->json(['message' => 'Waktu terlambat instruktur berhasil di-reset.']);
    }
}