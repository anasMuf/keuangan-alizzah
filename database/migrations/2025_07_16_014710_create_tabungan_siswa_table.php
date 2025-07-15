<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tabungan_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->references('id')->on('siakad_alizzah.siswa')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->date('tanggal')->default(now());
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungan_siswa');
    }
};
