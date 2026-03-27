<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proyeks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek');
            $table->foreignId('departemen_id')->constrained('departemen')->cascadeOnDelete();
            $table->date('waktu_mulai')->nullable();
            $table->date('waktu_selesai')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('proyeks'); }
};
