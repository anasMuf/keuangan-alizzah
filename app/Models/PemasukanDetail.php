<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemasukanDetail extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = "pemasukan_detail";
    protected $guarded = ['id'];

    public function pemasukan_pembayaran()
    {
        return $this->hasOne(PemasukanPembayaran::class, 'pemasukan_detail_id', 'id');
    }

    public function pemasukan(): BelongsTo
    {
        return $this->belongsTo(Pemasukan::class, 'pemasukan_id', 'id');
    }

    public function pos_pemasukan(): BelongsTo
    {
        return $this->belongsTo(PosPemasukan::class, 'pos_pemasukan_id', 'id');
    }

    public function tagihan_siswa(): BelongsTo
    {
        return $this->belongsTo(TagihanSiswa::class, 'tagihan_siswa_id', 'id');
    }
}
