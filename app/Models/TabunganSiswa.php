<?php

namespace App\Models;

use App\Models\SIAKAD\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TabunganSiswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'tabungan_siswa';
    protected $guarded = ['id'];

    // protected $appends = ['saldo'];

    // public function getSaldoAttribute()
    // {
    //     return $this->debit - $this->kredit;
    // }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}
