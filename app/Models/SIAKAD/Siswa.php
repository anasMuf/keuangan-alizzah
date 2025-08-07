<?php

namespace App\Models\SIAKAD;

use App\Models\SiswaDispensasi;
use App\Models\TabunganSiswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory,SoftDeletes;

    protected $connection = 'mysql_siakad';
    protected $table = "siswa";

    protected $guarded = ['id'];

    protected $appends = ['alamat_lengkap_format', 'alamat_lengkap_string_format'];//,'saldo_tabungan'];

    // protected $casts = [
    //     'tanggal_lahir' => 'date'
    // ];

    public function siswa_kelas() {
        return $this->hasMany(SiswaKelas::class);
    }

    public function siswa_dispensasi(){
        return $this->hasMany(SiswaDispensasi::class);
    }

    public function tabungan_siswa()
    {
        return $this->hasMany(TabunganSiswa::class, 'siswa_id', 'id');
    }

    // Relationships Wilayah
    public function provinsi()
    {
        return $this->belongsTo(\App\Models\SIAKAD\Provinsi::class);
    }

    public function kabupatenKota()
    {
        return $this->belongsTo(\App\Models\SIAKAD\KabupatenKota::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(\App\Models\SIAKAD\Kecamatan::class);
    }

    public function desa()
    {
        return $this->belongsTo(\App\Models\SIAKAD\Desa::class);
    }

    // Accessor untuk alamat lengkap
    public function getAlamatLengkapFormatAttribute()
    {
        $alamat = $this->alamat_lengkap;

        if ($this->desa) $alamat .= ", {$this->desa->nama}";
        if ($this->kecamatan) $alamat .= ", {$this->kecamatan->nama}";
        if ($this->kabupatenKota) $alamat .= ", {$this->kabupatenKota->nama}";
        if ($this->provinsi) $alamat .= ", {$this->provinsi->nama}";

        return $alamat;
    }
    public function getAlamatLengkapStringFormatAttribute()
    {
        $alamat = $this->alamat_lengkap;

        if ($this->desa) $alamat .= ", {$this->desa}";
        if ($this->kecamatan) $alamat .= ", {$this->kecamatan}";
        if ($this->kabupatenKota) $alamat .= ", {$this->kabupatenKota}";
        if ($this->provinsi) $alamat .= ", {$this->provinsi}";

        return $alamat;
    }

    // public function getSaldoTabunganAttribute()
    // {
    //     $lastTabungan = $this->tabungan_siswa()->orderByDesc('id')->first();
    //     return $lastTabungan ? $lastTabungan->saldo : 0;
    // }
}
