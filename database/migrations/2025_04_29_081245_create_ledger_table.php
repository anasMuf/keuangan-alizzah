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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('sumber_tabel');
            $table->bigInteger('referensi_id');
            $table->enum('tipe',['in','out']);
            $table->enum('jenis_akun',['aset','liabilitas','ekuitas','pendapatan','beban','hutang','piutang']);
            $table->dateTime('trx_date');
            $table->text('keterangan')->nullable();
            $table->decimal('debit',15,2);
            $table->decimal('kredit',15,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
