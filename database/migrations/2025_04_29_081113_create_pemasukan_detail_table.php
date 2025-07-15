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
        Schema::create('pemasukan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasukan_id')->references('id')->on('pemasukan')->cascadeOnDelete();
            $table->foreignId('tagihan_siswa_id')->nullable()->references('id')->on('tagihan_siswa')->cascadeOnDelete();
            $table->foreignId('pos_pemasukan_id')->nullable()->references('id')->on('pos_pemasukan')->cascadeOnDelete();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->decimal('diskon_persen', 15, 2)->default(0);
            $table->decimal('diskon_nominal', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
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
        Schema::dropIfExists('pemasukan_detail');
    }
};
