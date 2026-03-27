<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('presensi_gym', function (Blueprint $table) {
            $table->bigIncrements('id_presensi_gym');
            $table->unsignedBigInteger('id_booking_gym');
            $table->string('no_struk')->unique();
            $table->date('tanggal');

            $table->foreign('id_booking_gym')->references('id_booking_gym')->on('booking_gym')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('presensi_gym'); }
};
