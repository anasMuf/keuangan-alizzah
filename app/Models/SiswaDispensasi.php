<?php

namespace App\Models;

use App\Models\SIAKAD\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaDispensasi extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = "siswa_dispensasi";
    protected $guarded = ['id'];

    public function siswa(): BelongsTo {
        return $this->belongsTo(Siswa::class);
    }
    public function kategori_dispensasi(): BelongsTo {
        return $this->belongsTo(KategoriDispensasi::class);
    }
    public function pos_pemasukan(): BelongsTo {
        return $this->belongsTo(PosPemasukan::class);
    }
}
