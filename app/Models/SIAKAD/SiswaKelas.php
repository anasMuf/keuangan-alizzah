<?php

namespace App\Models\SIAKAD;

use App\Models\TagihanSiswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaKelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql_siakad';
    protected $table = 'siswa_kelas';

    protected $guarded = ['id'];

    public function tagihan_siswa(){
        return $this->hasMany(TagihanSiswa::class);
    }

    public function pemasukan(){
        return $this->hasMany(TahunAjaran::class,'siswa_kelas_id','id');
    }

    public function siswa(){
        return $this->belongsTo(Siswa::class);
    }
    public function kelas(){
        return $this->belongsTo(Kelas::class);
    }
    public function tahun_ajaran(){
        return $this->belongsTo(TahunAjaran::class);
    }
}
