<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presensi_kelas', function (Blueprint $table) {
            $table->bigIncrements('id_presensi_kelas');
            $table->unsignedBigInteger('id_booking_kelas');
            $table->string('no_struk')->unique();
            $table->date('tanggal');

            $table->foreign('id_booking_kelas')->references('id_booking_kelas')->on('booking_kelas')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('presensi_kelas'); }
};
