<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->bigIncrements('id_izin');
            $table->unsignedBigInteger('id_instruktur');
            $table->unsignedBigInteger('id_jadwal_harian');
            $table->text('detail_izin')->nullable();
            $table->date('tanggal_izin');
            $table->string('konfirmasi')->default('pending');
            $table->timestamps();

            $table->foreign('id_instruktur')->references('id_instruktur')->on('instruktur')->cascadeOnDelete();
            $table->foreign('id_jadwal_harian')->references('id_jadwal_harian')->on('jadwal_harian')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('izin'); }
};
