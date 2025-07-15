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
        Schema::create('jenjang_pos_pemasukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_pemasukan_id')->references('id')->on('pos_pemasukan')->cascadeOnDelete();
            $table->foreignId('jenjang_id')->references('id')->on('siakad_alizzah.jenjang')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->references('id')->on('siakad_alizzah.tahun_ajaran')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenjang_pos_pemasukan');
    }
};
