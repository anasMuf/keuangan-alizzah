<?php

namespace App\Models;

use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'pengeluaran';
    protected $guarded = ['id'];

    public function bulan()
    {
        return $this->belongsTo(Bulan::class, 'bulan_id');
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public static function generateNoTransaksi($tanggal): string
    {
        $prefix = 'TRXOUT-' . date('Ymd', strtotime($tanggal)) . '-';

        // Hitung transaksi pada hari ini
        $countToday = self::whereDate('created_at', date('Y-m-d', strtotime($tanggal)))->count();

        // Tambah 1 karena yang baru akan disimpan
        $number = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $number;
    }
}
