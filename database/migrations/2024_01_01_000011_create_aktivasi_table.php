<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aktivasi', function (Blueprint $table) {
            $table->bigIncrements('id_aktivasi');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_pegawai');
            $table->string('no_struk')->unique();
            $table->date('tanggal_aktivasi');
            $table->decimal('harga', 15, 2)->default(0);
            $table->date('masa_aktif')->nullable();
            $table->timestamps();

            $table->foreign('id_member')->references('id_member')->on('member')->cascadeOnDelete();
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('aktivasi'); }
};
