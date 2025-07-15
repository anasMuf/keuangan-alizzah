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
        Schema::create('tagihan_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->references('id')->on('siakad_alizzah.tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('bulan_id')->nullable()->references('id')->on('bulan')->cascadeOnDelete();
            $table->foreignId('siswa_kelas_id')->references('id')->on('siakad_alizzah.siswa_kelas')->cascadeOnDelete();
            $table->foreignId('pos_pemasukan_id')->references('id')->on('pos_pemasukan')->cascadeOnDelete();
            $table->date('tanggal_tagihan')->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->foreignId('siswa_dispensasi_id')->nullable()->references('id')->on('siswa_dispensasi')->cascadeOnDelete();
            $table->decimal('nominal_awal',15,2)->default(0);
            $table->decimal('diskon_persen',15,2)->default(0);
            $table->decimal('diskon_nominal',15,2)->default(0);
            $table->decimal('nominal',15,2)->default(0);
            $table->integer('jumlah_harus_dibayar')->default(0);
            $table->enum('status', ['belum_bayar','belum_lunas', 'lunas'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // indexes
            $table->index(['tahun_ajaran_id', 'bulan_id', 'siswa_kelas_id', 'pos_pemasukan_id'], 'idx_tagihan_siswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_siswa');
    }
};
