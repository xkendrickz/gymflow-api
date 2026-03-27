<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\Instruktur;
use App\Models\Pegawai;

class AuthController extends Controller
{
    public function loginAndroid(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $member = Member::where('username', $request->username)->first();
        if ($member && Hash::check($request->password, $member->password)) {
            $token = $member->createToken('mobile')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil.',
                'token'   => $token,
                'userType'=> 'member',
                'data'    => $member,
            ]);
        }

        $instruktur = Instruktur::where('username', $request->username)->first();
        if ($instruktur && Hash::check($request->password, $instruktur->password)) {
            $token = $instruktur->createToken('mobile')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil.',
                'token'   => $token,
                'userType'=> 'instruktur',
                'data'    => $instruktur,
            ]);
        }

        // Try pegawai
        $pegawai = Pegawai::where('username', $request->username)->first();
        if ($pegawai && Hash::check($request->password, $pegawai->password)) {
            $token = $pegawai->createToken('mobile')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil.',
                'token'   => $token,
                'userType'=> 'pegawai',
                'data'    => $pegawai,
            ]);
        }

        return response()->json(['message' => 'Username atau password salah.'], 401);
    }

    public function loginWeb(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $pegawai = Pegawai::where('username', $request->username)->first();
        if (!$pegawai || !Hash::check($request->password, $pegawai->password)) {
            return response()->json(['message' => 'Username atau password salah.'], 401);
        }

        $token = $pegawai->createToken('web')->plainTextToken;

        return response()->json([
            'message'  => 'Login berhasil.',
            'token'    => $token,
            'userType' => 'pegawai',
            'data'     => $pegawai,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }
}