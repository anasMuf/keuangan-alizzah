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
        Schema::create('kategori_dispensasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->text('keterangan')->nullable();
            $table->decimal('persentase_default',15,2)->default(0);
            $table->decimal('nominal_default',15,2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_dispensasi');
    }
};
