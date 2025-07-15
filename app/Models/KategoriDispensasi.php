<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriDispensasi extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = "kategori_dispensasi";
    protected $guarded = ['id'];

    public function siswa_dispensasi(): HasMany {
        return $this->hasMany(SiswaDispensasi::class);
    }
}
