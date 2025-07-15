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
}
