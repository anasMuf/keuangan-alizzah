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
        Schema::create('pemasukan_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemasukan_id')->references('id')->on('pemasukan')->cascadeOnDelete();
            $table->foreignId('pemasukan_detail_id')->references('id')->on('pemasukan_detail')->cascadeOnDelete();
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
        Schema::dropIfExists('pemasukan_pembayaran');
    }
};
