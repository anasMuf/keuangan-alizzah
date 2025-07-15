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
        Schema::create('siswa_dispensasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->references('id')->on('siakad_alizzah.siswa')->cascadeOnDelete();
            $table->foreignId('kategori_dispensasi_id')->references('id')->on('kategori_dispensasi')->cascadeOnDelete();
            $table->foreignId('pos_pemasukan_id')->references('id')->on('pos_pemasukan')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('persentase_overide',15,2)->nullable();
            $table->decimal('nominal_overide',15,2)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status',['aktif','nonaktif']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_dispensasi');
    }
};
