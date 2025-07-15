<?php

namespace App\Models\SIAKAD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $connection = 'mysql_siakad';
    protected $table = 'provinsi';
    protected $fillable = ['kode', 'nama'];

    public function kabupatenKota()
    {
        return $this->hasMany(KabupatenKota::class);
    }
}
