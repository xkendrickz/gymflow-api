<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deposit_paket', function (Blueprint $table) {
            $table->bigIncrements('id_deposit_paket');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_kelas');
            $table->string('no_struk')->unique();
            $table->decimal('harga', 15, 2)->default(0);
            $table->date('tanggal');
            $table->decimal('deposit', 15, 2)->default(0);
            $table->integer('jumlah_deposit_paket')->default(0);
            $table->date('berlaku_sampai')->nullable();
            $table->timestamps();

            $table->foreign('id_member')->references('id_member')->on('member')->cascadeOnDelete();
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->cascadeOnDelete();
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('deposit_paket'); }
};
