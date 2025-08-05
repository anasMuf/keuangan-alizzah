<?php

namespace App\Models;

use App\Models\SIAKAD\SiswaKelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaEkstrakulikuler extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'siswa_ekstrakulikuler';
    protected $guarded = ['id'];

    public function pos_pemasukan()
    {
        return $this->belongsTo(PosPemasukan::class, 'pos_pemasukan_id', 'id');
    }

    public function siswa_kelas()
    {
        return $this->belongsTo(SiswaKelas::class, 'siswa_kelas_id', 'id');
    }
}
