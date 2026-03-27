<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jadwal_harian', function (Blueprint $table) {
            $table->bigIncrements('id_jadwal_harian');
            $table->unsignedBigInteger('id_jadwal_umum');
            $table->string('hari');
            $table->timestamps();

            $table->foreign('id_jadwal_umum')->references('id_jadwal_umum')->on('jadwal_umum')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('jadwal_harian'); }
};
