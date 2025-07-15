<?php

namespace App\Models;

use App\Models\SIAKAD\Jenjang;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenjangPosPemasukan extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'jenjang_pos_pemasukan';
    protected $guarded = ['id'];

    public function jenjang_pos_pemasukan_detail(): HasMany
    {
        return $this->hasMany(JenjangPosPemasukanDetail::class, 'jenjang_pos_pemasukan_id', 'id');
    }

    public function pos_pemasukan(): BelongsTo
    {
        return $this->belongsTo(PosPemasukan::class, 'pos_pemasukan_id', 'id');
    }

    public function jenjang(): BelongsTo
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id', 'id');
    }

    public function tahun_ajaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

    public static function store($request) {
        return JenjangPosPemasukan::create([
            'pos_pemasukan_id' => $request->pos_pemasukan_id,
            'jenjang_id' => $request->jenjang_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
        ]) ?: false;
    }
}
