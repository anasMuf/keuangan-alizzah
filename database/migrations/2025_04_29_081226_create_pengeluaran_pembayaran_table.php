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
        Schema::create('pengeluaran_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengeluaran_id')->references('id')->on('pengeluaran')->cascadeOnDelete();
            $table->foreignId('pengeluaran_detail_id')->references('id')->on('pengeluaran_detail')->cascadeOnDelete();
            $table->dateTime('tanggal');
            $table->decimal('nominal',15,2);
            $table->enum('metode',['tunai','transfer','lainnya'])->nullable();
            $table->text('bukti')->nullable();
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
        Schema::dropIfExists('pengeluaran_pembayaran');
    }
};
