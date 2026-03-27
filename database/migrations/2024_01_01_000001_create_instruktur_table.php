<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('instruktur', function (Blueprint $table) {
            $table->bigIncrements('id_instruktur');
            $table->string('nama_instruktur');
            $table->date('tanggal_lahir')->nullable();
            $table->time('waktu_terlambat')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
        });
    }
    public function down(): void { Schema::dropIfExists('instruktur'); }
};
