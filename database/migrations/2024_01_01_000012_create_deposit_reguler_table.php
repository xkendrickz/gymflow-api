<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deposit_reguler', function (Blueprint $table) {
            $table->bigIncrements('id_deposit_reguler');
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_pegawai');
            $table->string('no_struk')->unique();
            $table->date('tanggal');
            $table->decimal('deposit', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('total_deposit_reguler', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_member')->references('id_member')->on('member')->cascadeOnDelete();
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('deposit_reguler'); }
};
