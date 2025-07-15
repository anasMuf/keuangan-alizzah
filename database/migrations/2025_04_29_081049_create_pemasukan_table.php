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
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi',20);
            $table->foreignId('siswa_kelas_id')->references('id')->on('siakad_alizzah.siswa_kelas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->references('id')->on('siakad_alizzah.tahun_ajaran')->cascadeOnDelete();
            $table->dateTime('tanggal');
            $table->text('keterangan')->nullable();
            $table->decimal('total',15,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
