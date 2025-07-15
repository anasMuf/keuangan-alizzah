<?php

namespace App\Models\SIAKAD;

use App\Models\JenjangPosPemasukan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jenjang extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql_siakad';
    protected $table = "jenjang";

    protected $guarded = ['id'];

    public function kelas(): HasMany{
        return $this->hasMany(Kelas::class);
    }

    public function jenjang_pos_pemasukan(): HasMany
    {
        return $this->hasMany(JenjangPosPemasukan::class, 'jenjang_id', 'id');
    }
}
