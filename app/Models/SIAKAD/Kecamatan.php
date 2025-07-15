<?php

namespace App\Models\SIAKAD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $connection = 'mysql_siakad';
    protected $table = 'kecamatan';
    protected $fillable = ['kabupaten_kota_id', 'kode', 'nama'];

    public function kabupatenKota()
    {
        return $this->belongsTo(KabupatenKota::class);
    }

    public function desa()
    {
        return $this->hasMany(Desa::class);
    }
}
