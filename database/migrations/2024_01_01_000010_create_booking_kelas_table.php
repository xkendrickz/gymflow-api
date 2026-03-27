<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_kelas', function (Blueprint $table) {
            $table->bigIncrements('id_booking_kelas');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_jadwal_harian');
            $table->string('no_booking')->unique();
            $table->string('jenis')->nullable();
            $table->string('status')->default('aktif');

            $table->foreign('id_member')->references('id_member')->on('member')->cascadeOnDelete();
            $table->foreign('id_jadwal_harian')->references('id_jadwal_harian')->on('jadwal_harian')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('booking_kelas'); }
};
