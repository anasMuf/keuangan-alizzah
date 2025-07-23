<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pos extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'pos';
    protected $guarded = ['id'];

    public function pos_pemasukan()
    {
        return $this->hasMany(PosPemasukan::class, 'pos_id', 'id');
    }

    public function pos_pengeluaran()
    {
        return $this->hasMany(PosPengeluaran::class, 'pos_id', 'id');
    }
}
