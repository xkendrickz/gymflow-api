<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DepositReguler;
use App\Models\Member;
use Carbon\Carbon;

class DepositRegulerController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_member'  => 'required|exists:member,id_member',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'deposit'    => 'required|numeric|min:1',
        ]);

        $member        = Member::findOrFail($validated['id_member']);
        $sisaDeposit   = $member->sisa_deposit_reguler;
        $deposit       = $validated['deposit'];

        $bonus = 0;
        if ($sisaDeposit >= 500000 && $deposit >= 3000000) {
            $bonus = floor($deposit / 3000000) * 300000;
        }

        $now               = Carbon::now();
        $latestId          = DB::table('deposit_reguler')->max('id_deposit_reguler') ?? 0;
        $no_struk          = $now->format('y') . '.' . $now->format('m') . '.' . ($latestId + 1);
        $totalDepositBaru  = $sisaDeposit + $deposit + $bonus;

        $depositReguler = new DepositReguler;
        $depositReguler->timestamps = false;
        $depositReguler->fill([
            'id_member'            => $validated['id_member'],
            'id_pegawai'           => $validated['id_pegawai'],
            'no_struk'             => $no_struk,
            'tanggal'              => $now->toDateString(),
            'deposit'              => $deposit,
            'bonus'                => $bonus,
            'total_deposit_reguler'=> $totalDepositBaru,
        ])->save();

        $member->timestamps = false;
        $member->sisa_deposit_reguler = $totalDepositBaru;
        $member->save();

        return response()->json(['message' => 'Deposit reguler berhasil disimpan.'], 201);
    }
}