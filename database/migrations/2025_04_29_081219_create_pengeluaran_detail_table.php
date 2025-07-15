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
        Schema::create('pengeluaran_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengeluaran_id')->references('id')->on('pengeluaran')->cascadeOnDelete();
            $table->foreignId('pos_pengeluaran_id')->references('id')->on('pos_pengeluaran')->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->decimal('nominal',15,2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_detail');
    }
};
