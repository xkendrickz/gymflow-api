<?php
use Illuminate\Support\Facades\Route;

// ── Public routes ──
Route::name('api.')->group(function () {

    Route::post('loginAndroid', 'App\Http\Controllers\AuthController@loginAndroid')->name('loginAndroid');
    Route::post('loginWeb', 'App\Http\Controllers\AuthController@loginWeb')->name('loginWeb');

    // ── Protected routes ──
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', 'App\Http\Controllers\AuthController@logout')->name('logout');

        // Resources
        Route::apiResource('/instruktur', App\Http\Controllers\InstrukturController::class);
        Route::apiResource('/member', App\Http\Controllers\MemberController::class);
        Route::apiResource('/pegawai', App\Http\Controllers\PegawaiController::class);
        Route::apiResource('/kelas', App\Http\Controllers\KelasController::class);
        Route::apiResource('/izin', App\Http\Controllers\IzinController::class);
        Route::apiResource('/jadwalUmum', App\Http\Controllers\JadwalUmumController::class);
        Route::apiResource('/jadwalHarian', App\Http\Controllers\JadwalHarianController::class);
        Route::apiResource('/aktivasi', App\Http\Controllers\AktivasiController::class);
        Route::apiResource('/depositReguler', App\Http\Controllers\DepositRegulerController::class);
        Route::apiResource('/depositKelas', App\Http\Controllers\DepositKelasController::class);
        Route::apiResource('/bookingGym', App\Http\Controllers\BookingGymController::class);
        Route::apiResource('/presensiInstruktur', App\Http\Controllers\PresensiInstrukturController::class);
        Route::apiResource('/presensiKelas', App\Http\Controllers\PresensiKelasController::class);
        Route::apiResource('/presensiGym', App\Http\Controllers\PresensiGymController::class);

        // History
        Route::get('historyInstruktur/{id}', 'App\Http\Controllers\HistoryController@historyInstruktur')->name('historyInstruktur');
        Route::get('historyMemberPresensi/{id}', 'App\Http\Controllers\HistoryController@historyMemberPresensi')->name('historyMemberPresensi');
        Route::get('historyMemberTransaksi/{id}', 'App\Http\Controllers\HistoryController@historyMemberTransaksi')->name('historyMemberTransaksi');

        // Profiles
        Route::get('profilePegawai/{id}', 'App\Http\Controllers\PegawaiController@profilePegawai')->name('profilePegawai');
        Route::get('profileMember/{id}', 'App\Http\Controllers\MemberController@profileMember')->name('profileMember');
        Route::get('profileInstruktur/{id}', 'App\Http\Controllers\InstrukturController@profileInstruktur')->name('profileInstruktur');

        // Laporan
        Route::get('laporanKinerjaInstruktur/{bulan}/{tahun}', 'App\Http\Controllers\LaporanController@laporanKinerjaInstruktur')->name('laporanKinerjaInstruktur');
        Route::get('laporanAktivitasGym/{bulan}/{tahun}', 'App\Http\Controllers\LaporanController@laporanAktivitasGym')->name('laporanAktivitasGym');
        Route::get('dropdownAktivitasGym', 'App\Http\Controllers\LaporanController@dropdownAktivitasGym')->name('dropdownAktivitasGym');
        Route::get('laporanAktivitasKelas/{bulan}/{tahun}', 'App\Http\Controllers\LaporanController@laporanAktivitasKelas')->name('laporanAktivitasKelas');
        Route::get('dropdownAktivitasKelas', 'App\Http\Controllers\LaporanController@dropdownAktivitasKelas')->name('dropdownAktivitasKelas');
        Route::get('laporanPendapatan/{tahun}', 'App\Http\Controllers\LaporanController@laporanPendapatan')->name('laporanPendapatan');
        Route::get('dropdownPendapatan', 'App\Http\Controllers\LaporanController@dropdownPendapatan')->name('dropdownPendapatan');

        // Others
        Route::get('cetakStruk/{id}', 'App\Http\Controllers\PresensiKelasController@cetakStruk')->name('cetakStruk');
        Route::delete('bookingGym/{id_member}/{tanggal}', 'App\Http\Controllers\BookingGymController@destroy')->name('bookingGym.customDestroy');
        Route::get('indexAktivasi', 'App\Http\Controllers\ResetController@indexAktivasi')->name('indexAktivasi');
        Route::get('indexDeposit', 'App\Http\Controllers\ResetController@indexDeposit')->name('indexDeposit');
        Route::post('resetMember', 'App\Http\Controllers\ResetController@resetMember')->name('resetMember');
        Route::post('resetInstruktur', 'App\Http\Controllers\ResetController@resetInstruktur')->name('resetInstruktur');
    });
});