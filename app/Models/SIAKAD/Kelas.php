<?php

namespace App\Models\SIAKAD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql_siakad';
    protected $table = "kelas";

    protected $guarded = ['id'];

    public function siswa_kelas() {
        return $this->hasMany(SiswaKelas::class);
    }

    public function jenjang(){
        return $this->belongsTo(Jenjang::class);
    }
}
