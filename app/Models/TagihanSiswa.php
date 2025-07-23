<?php

namespace App\Models;

use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagihanSiswa extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'tagihan_siswa';
    protected $guarded = ['id'];

    protected $appends = ['sisa_pembayaran'];

    public function getSisaPembayaranAttribute()
    {
        $totalBayar = $this->pemasukan_detail->sum(function ($detail) {
            return $detail->pemasukan_pembayaran ? $detail->pemasukan_pembayaran->nominal : 0;
        });

        return $this->nominal - $totalBayar;
    }

    public function pemasukan_detail()
    {
        return $this->hasMany(PemasukanDetail::class, 'tagihan_siswa_id', 'id');
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function bulan()
    {
        return $this->belongsTo(Bulan::class);
    }

    public function siswa_kelas()
    {
        return $this->belongsTo(SiswaKelas::class);
    }

    public function pos_pemasukan()
    {
        return $this->belongsTo(PosPemasukan::class);
    }

    public function siswa_dispensasi()
    {
        return $this->belongsTo(SiswaDispensasi::class);
    }
}
