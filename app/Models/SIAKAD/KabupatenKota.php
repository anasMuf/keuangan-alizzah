<?php

namespace App\Models\SIAKAD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabupatenKota extends Model
{
    use HasFactory;

    protected $connection = 'mysql_siakad';
    protected $table = 'kabupaten_kota';
    protected $fillable = ['provinsi_id', 'kode', 'nama'];

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
    }

}
