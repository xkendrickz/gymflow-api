<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member', function (Blueprint $table) {
            $table->bigIncrements('id_member');
            $table->string('member_id')->unique()->nullable();
            $table->string('nama_member');
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->date('tanggal_daftar')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('aktif');
            $table->decimal('sisa_deposit_reguler', 15, 2)->default(0);
            $table->decimal('sisa_deposit_paket', 15, 2)->default(0);
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
        });
    }
    public function down(): void { Schema::dropIfExists('member'); }
};
