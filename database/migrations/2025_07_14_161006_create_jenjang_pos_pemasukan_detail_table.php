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
        Schema::create('jenjang_pos_pemasukan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenjang_pos_pemasukan_id')->references('id')->on('jenjang_pos_pemasukan')->cascadeOnDelete();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenjang_pos_pemasukan_detail');
    }
};
