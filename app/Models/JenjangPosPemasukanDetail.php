<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenjangPosPemasukanDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'jenjang_pos_pemasukan_detail';
    protected $guarded = ['id'];

    public function jenjang_pos_pemasukan_nominal()
    {
        return $this->hasMany(JenjangPosPemasukanNominal::class, 'jenjang_pos_pemasukan_detail_id', 'id');
    }

    public function jenjang_pos_pemasukan() : BelongsTo
    {
        return $this->belongsTo(JenjangPosPemasukan::class, 'jenjang_pos_pemasukan_id', 'id');
    }
}
