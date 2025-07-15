<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bulan extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'bulan';
    protected $guarded = ['id'];


    public function tagihan_siswa(){
        return $this->hasMany(TagihanSiswa::class);
    }

    // public function pemasukan(){
    //     return $this->hasMany(TahunAjaran::class,'bulan_id','id');
    // }
}
