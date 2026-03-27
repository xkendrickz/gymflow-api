<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jadwal_umum', function (Blueprint $table) {
            $table->bigIncrements('id_jadwal_umum');
            $table->unsignedBigInteger('id_instruktur');
            $table->unsignedBigInteger('id_kelas');
            $table->time('jam');
            $table->string('hari');
            $table->timestamps();

            $table->foreign('id_instruktur')->references('id_instruktur')->on('instruktur')->cascadeOnDelete();
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_umum'); }
};
