<?php

namespace App\Models\SIAKAD;

use App\Models\JenjangPosPemasukan;
use App\Models\Pengeluaran;
use App\Models\TagihanSiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql_siakad';
    protected $table = "tahun_ajaran";

    protected $guarded = ['id'];

    public function tagihan_siswa(){
        return $this->hasMany(TagihanSiswa::class);
    }

    public function pengeluaran(){
        return $this->hasMany(Pengeluaran::class,'tahun_ajaran_id','id');
    }

    public function pemasukan(){
        return $this->hasMany(TahunAjaran::class,'tahun_ajaran_id','id');
    }

    public function siswa_kelas(): HasMany {
        return $this->hasMany(SiswaKelas::class);
    }

    public function jenjang_pos_pemasukan(): HasMany
    {
        return $this->hasMany(JenjangPosPemasukan::class, 'tahun_ajaran_id', 'id');
    }
}
