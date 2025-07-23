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
        Schema::create('pos_pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori',['bebean_operasional','beban_administrasi'])->nullable();
            $table->string('nama_pos_pengeluaran');
            $table->foreignId('pos_id')->references('id')->on('pos')->cascadeOnDelete();
            $table->foreignId('pos_pemasukan_id')->references('id')->on('pos_pemasukan')->cascadeOnDelete();
            $table->decimal('nominal_valid',15,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_pengeluaran');
    }
};
