<?php

namespace App\Services;

use App\Models\Provinsi;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Support\Facades\Log;

class WilayahLookupService
{
    public function findProvinsiByName($namaProvinsi)
    {
        if (empty($namaProvinsi)) {
            return null;
        }

        // Normalisasi nama untuk pencarian yang lebih akurat
        $namaProvinsi = $this->normalizeString($namaProvinsi);

        // Coba exact match dulu
        $provinsi = Provinsi::whereRaw('LOWER(nama) = ?', [strtolower($namaProvinsi)])->first();

        if (!$provinsi) {
            // Coba partial match
            $provinsi = Provinsi::whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($namaProvinsi) . '%'])->first();
        }

        return $provinsi;
    }

    public function findKabupatenKotaByName($namaKabupatenKota, $provinsiId = null)
    {
        if (empty($namaKabupatenKota)) {
            return null;
        }

        $namaKabupatenKota = $this->normalizeString($namaKabupatenKota);

        $query = KabupatenKota::query();

        if ($provinsiId) {
            $query->where('provinsi_id', $provinsiId);
        }

        // Exact match
        $kabupatenKota = $query->whereRaw('LOWER(nama) = ?', [strtolower($namaKabupatenKota)])->first();

        if (!$kabupatenKota) {
            // Partial match
            $kabupatenKota = $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($namaKabupatenKota) . '%'])->first();
        }

        return $kabupatenKota;
    }

    public function findKecamatanByName($namaKecamatan, $kabupatenKotaId = null)
    {
        if (empty($namaKecamatan)) {
            return null;
        }

        $namaKecamatan = $this->normalizeString($namaKecamatan);

        $query = Kecamatan::query();

        if ($kabupatenKotaId) {
            $query->where('kabupaten_kota_id', $kabupatenKotaId);
        }

        // Exact match
        $kecamatan = $query->whereRaw('LOWER(nama) = ?', [strtolower($namaKecamatan)])->first();

        if (!$kecamatan) {
            // Partial match
            $kecamatan = $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($namaKecamatan) . '%'])->first();
        }

        return $kecamatan;
    }

    public function findDesaByName($namaDesa, $kecamatanId = null)
    {
        if (empty($namaDesa)) {
            return null;
        }

        $namaDesa = $this->normalizeString($namaDesa);

        $query = Desa::query();

        if ($kecamatanId) {
            $query->where('kecamatan_id', $kecamatanId);
        }

        // Exact match
        $desa = $query->whereRaw('LOWER(nama) = ?', [strtolower($namaDesa)])->first();

        if (!$desa) {
            // Partial match
            $desa = $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($namaDesa) . '%'])->first();
        }

        return $desa;
    }

    private function normalizeString($string)
    {
        // Remove extra spaces and trim
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    public function validateWilayahChain($provinsi, $kabupatenKota, $kecamatan, $desa)
    {
        $errors = [];

        // Validasi chain relationship
        if ($kabupatenKota && $provinsi && $kabupatenKota->provinsi_id != $provinsi->id) {
            $errors[] = "Kabupaten/Kota '{$kabupatenKota->nama}' tidak berada di Provinsi '{$provinsi->nama}'";
        }

        if ($kecamatan && $kabupatenKota && $kecamatan->kabupaten_kota_id != $kabupatenKota->id) {
            $errors[] = "Kecamatan '{$kecamatan->nama}' tidak berada di Kabupaten/Kota '{$kabupatenKota->nama}'";
        }

        if ($desa && $kecamatan && $desa->kecamatan_id != $kecamatan->id) {
            $errors[] = "Desa '{$desa->nama}' tidak berada di Kecamatan '{$kecamatan->nama}'";
        }

        return $errors;
    }
}
