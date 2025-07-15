<?php

namespace App\Models\SIAKAD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    protected $connection = 'mysql_siakad';
    protected $table = 'desa';
    protected $fillable = ['kecamatan_id', 'kode', 'nama'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}
