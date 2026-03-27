<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->bigIncrements('id_pegawai');
            $table->unsignedTinyInteger('id_role')->default(1); // 1=admin, 2=mo, 3=kasir
            $table->string('nama_pegawai');
            $table->date('tanggal_lahir')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
        });
    }
    public function down(): void { Schema::dropIfExists('pegawai'); }
};
