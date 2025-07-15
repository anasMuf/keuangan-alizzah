<?php

namespace App\Models;

use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pemasukan extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'pemasukan';
    protected $guarded = ['id'];

    protected $appends = ['status_pembayaran'];

    public function getStatusPembayaranAttribute(){
        $pemasukan_pembayaran = PemasukanPembayaran::selectRAW('sum(nominal) as total_bayar')->where('pemasukan_id',$this->id)->groupBy('pemasukan_id')->first();
        $selisih = $this->total - $pemasukan_pembayaran->total_bayar;
        if($selisih == 0){
            return 'LUNAS';
        }elseif($selisih > 0){
            return 'BELUM LUNAS';
        }
    }

    public function pemasukan_detail(): HasMany
    {
        return $this->hasMany(PemasukanDetail::class, 'pemasukan_id', 'id');
    }

    public function siswa_kelas(){
        return $this->belongsTo(SiswaKelas::class,'siswa_kelas_id','id');
    }
    public function bulan(){
        return $this->belongsTo(Bulan::class,'bulan_id','id');
    }
    public function tahun_ajaran(){
        return $this->belongsTo(TahunAjaran::class,'tahun_ajaran_id','id');
    }


    public static function generateNoTransaksi(): string
    {
        $prefix = 'TRXIN-' . date('Ymd') . '-';

        // Hitung transaksi pada hari ini
        $countToday = self::whereDate('created_at', date('Y-m-d'))->count();

        // Tambah 1 karena yang baru akan disimpan
        $number = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $number;
    }
}
