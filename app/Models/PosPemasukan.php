<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosPemasukan extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'pos_pemasukan';
    protected $guarded = ['id'];

    protected $appends = [
        'saldo_tabungan',
        'saldo_tabungan_wajib',
    ];

    public function getSaldoTabunganAttribute()
    {
        $saldo = 0;
        if($this->tabungan){
            foreach($this->pemasukan_detail as $detail){
                $saldo += $detail->subtotal;
            }
        }
        return $saldo;
    }

    public function getSaldoTabunganWajibAttribute()
    {
        $saldoWajib = 0;
        if($this->tabungan && $this->wajib){
            foreach($this->tagihan_siswa as $tagihan){
                if($tagihan->status == 'lunas'){
                    $saldoWajib += $tagihan->nominal;
                }
            }
        }
        return $saldoWajib;
    }

    public function pos_pengeluaran()
    {
        return $this->hasOne(PosPengeluaran::class, 'pos_pemasukan_id', 'id');
    }

    public function tagihan_siswa(){
        return $this->hasMany(TagihanSiswa::class, 'pos_pemasukan_id', 'id');
    }

    public function jenjang_pos_pemasukan(): HasMany {
        return $this->hasMany(JenjangPosPemasukan::class, 'pos_pemasukan_id', 'id');
    }

    public function pemasukan_detail(): HasMany {
        return $this->hasMany(PemasukanDetail::class, 'pos_pemasukan_id', 'id');
    }

    public function siswa_dispensasi(): HasMany {
        return $this->hasMany(SiswaDispensasi::class, 'pos_pemasukan_id', 'id');
    }

    public function pos()
    {
        return $this->belongsTo(Pos::class, 'pos_id', 'id');
    }
}
