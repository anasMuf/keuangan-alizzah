<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemasukanPembayaran extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = "pemasukan_pembayaran";
    protected $guarded = ['id'];

    public function pemasukan_detail()
    {
        return $this->belongsTo(PemasukanDetail::class, 'pemasukan_detail_id', 'id');
    }

    public function pemasukan()
    {
        return $this->belongsTo(Pemasukan::class, 'pemasukan_id', 'id');
    }
}
