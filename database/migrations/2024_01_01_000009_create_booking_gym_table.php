<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_gym', function (Blueprint $table) {
            $table->bigIncrements('id_booking_gym');
            $table->unsignedBigInteger('id_member');
            $table->date('tanggal');
            $table->string('slot_waktu');
            $table->string('status')->default('aktif');

            $table->foreign('id_member')->references('id_member')->on('member')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('booking_gym'); }
};
