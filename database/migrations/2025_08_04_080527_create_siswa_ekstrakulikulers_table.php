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
        Schema::create('siswa_ekstrakulikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_kelas_id')
                ->references('id')->on('siakad_alizzah.siswa_kelas')
                ->cascadeOnDelete();
            $table->foreignId('pos_pemasukan_id')
                ->references('id')->on('pos_pemasukan')
                ->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->integer('ke')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_ekstrakulikuler');
    }
};
