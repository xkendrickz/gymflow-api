<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presensi_instruktur', function (Blueprint $table) {
            $table->bigIncrements('id_presensi_instruktur');
            $table->unsignedBigInteger('id_jadwal_harian');
            $table->timestamp('mulai_kelas')->nullable();
            $table->timestamp('selesai_kelas')->nullable();

            $table->foreign('id_jadwal_harian')->references('id_jadwal_harian')->on('jadwal_harian')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('presensi_instruktur'); }
};
