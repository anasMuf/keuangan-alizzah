<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WilayahService;

class WilayahController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function getProvinsi()
    {
        $provinsi = $this->wilayahService->getProvinsi();
        return response()->json($provinsi);
    }

    public function getKabupatenKota(Request $request)
    {
        $provinsiId = $request->get('provinsi_id');
        $kabupatenKota = $this->wilayahService->getKabupatenKota($provinsiId);
        return response()->json($kabupatenKota);
    }

    public function getKecamatan(Request $request)
    {
        $kabupatenKotaId = $request->get('kabupaten_kota_id');
        $kecamatan = $this->wilayahService->getKecamatan($kabupatenKotaId);
        return response()->json($kecamatan);
    }

    public function getDesa(Request $request)
    {
        $kecamatanId = $request->get('kecamatan_id');
        $desa = $this->wilayahService->getDesa($kecamatanId);
        return response()->json($desa);
    }

    public function syncWilayah()
    {
        $result = $this->wilayahService->syncAllWilayah();

        if ($result) {
            return response()->json(['message' => 'Data wilayah berhasil disinkronisasi']);
        } else {
            return response()->json(['message' => 'Gagal sinkronisasi data wilayah'], 500);
        }
    }
}
