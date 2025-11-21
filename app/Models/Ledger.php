<?php

namespace App\Models;

use App\Models\PosPemasukan;
use App\Models\Pemasukan;
use App\Models\TabunganSiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ledger extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = "ledgers";
    protected $guarded = ['id'];

    protected $fillable = [
        'sumber_tabel',
        'referensi_id',
        'tipe',
        'jenis_akun',
        'trx_date',
        'keterangan',
        'debit',
        'kredit'
    ];

    protected $dates = [
        'trx_date',
        'deleted_at'
    ];

    protected $casts = [
        'trx_date' => 'datetime',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2'
    ];

    // Relasi polymorphic untuk mendapatkan data sumber berdasarkan contoh data
    public function source()
    {
        switch ($this->sumber_tabel) {
            case 'pemasukan':
                return $this->belongsTo(Pemasukan::class, 'referensi_id');
            case 'tabungan_siswa':
                return $this->belongsTo(TabunganSiswa::class, 'referensi_id');
            case 'pengeluaran':
                return $this->belongsTo(Pengeluaran::class, 'referensi_id');
            default:
                return null;
        }
    }

    // Relasi untuk mendapatkan pos pemasukan melalui pemasukan
    public function posPemasukan()
    {
        return $this->hasOneThrough(
            PosPemasukan::class,
            Pemasukan::class,
            'id', // Foreign key di tabel pemasukan
            'id', // Foreign key di tabel pos_pemasukans
            'referensi_id', // Local key di tabel ledgers
            'pos_pemasukan_id' // Local key di tabel pemasukan
        )->where('ledgers.sumber_tabel', 'pemasukan');
    }

    // Relasi langsung ke pemasukan
    public function pemasukan()
    {
        return $this->belongsTo(Pemasukan::class, 'referensi_id')
                    ->where('sumber_tabel', 'pemasukan');
    }

    // Relasi langsung ke tabungan siswa
    public function tabunganSiswa()
    {
        return $this->belongsTo(TabunganSiswa::class, 'referensi_id')
                    ->where('sumber_tabel', 'tabungan_siswa');
    }

    // Scope untuk filter berdasarkan periode
    public function scopeWherePeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('trx_date', [$startDate, $endDate]);
    }

    // Scope untuk filter berdasarkan tipe
    public function scopeWhereTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Scope untuk filter berdasarkan jenis akun
    public function scopeWhereJenisAkun($query, $jenisAkun)
    {
        return $query->where('jenis_akun', $jenisAkun);
    }

    // Scope untuk filter berdasarkan sumber tabel
    public function scopeWhereSumberTabel($query, $sumberTabel)
    {
        return $query->where('sumber_tabel', $sumberTabel);
    }

    // Scope untuk pemasukan saja
    public function scopePemasukan($query)
    {
        return $query->where('sumber_tabel', 'pemasukan')
                    ->where('tipe', 'in')
                    ->where('jenis_akun', 'pendapatan');
    }

    // Scope untuk pengeluaran saja
    public function scopePengeluaran($query)
    {
        return $query->where(function($q) {
            $q->where('tipe', 'out')
              ->orWhere('jenis_akun', 'beban');
        });
    }

    // Method untuk mendapatkan nominal bersih (debit - kredit)
    public function getNominalAttribute()
    {
        return $this->debit - $this->kredit;
    }

    // Method untuk mendapatkan pos pemasukan dari relasi
    public function getPosItemAttribute()
    {
        if ($this->sumber_tabel === 'pemasukan' && $this->pemasukan) {
            return $this->pemasukan->posPemasukan;
        }
        return null;
    }

    // Method untuk cek apakah ini transaksi pemasukan
    public function isPemasukan()
    {
        return $this->sumber_tabel === 'pemasukan' &&
               $this->tipe === 'in' &&
               $this->jenis_akun === 'pendapatan';
    }

    // Method untuk cek apakah ini transaksi pengeluaran
    public function isPengeluaran()
    {
        return $this->tipe === 'out' || $this->jenis_akun === 'beban';
    }
}
