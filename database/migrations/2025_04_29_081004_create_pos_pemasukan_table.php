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
        Schema::create('pos_pemasukan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pos_pemasukan');
            $table->text('keterangan');
            $table->foreignId('pos_id')->references('id')->on('pos')->cascadeOnDelete();
            $table->boolean('tabungan')->default(false);
            $table->boolean('wajib')->default(true);
            $table->boolean('optional')->default(false);
            $table->enum('pembayaran',['sekali','harian','mingguan','bulanan','tahunan'])->nullable();
            $table->boolean('hari_aktif')->default(false);
            $table->decimal('nominal_valid',15,2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_pemasukan');
    }
};
