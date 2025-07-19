<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenjangPosPemasukanNominal extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'jenjang_pos_pemasukan_nominal';
    protected $guarded = ['id'];

    public function jenjang_pos_pemasukan_detail()
    {
        return $this->belongsTo(JenjangPosPemasukanDetail::class, 'jenjang_pos_pemasukan_detail_id', 'id');
    }
    public function bulan()
    {
        return $this->belongsTo(Bulan::class, 'bulan_id', 'id');
    }
}
