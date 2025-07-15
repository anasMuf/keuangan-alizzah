<?php

namespace App\Services;

use App\Models\Provinsi;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WilayahService
{
    private $apiBaseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    public function getProvinsi()
    {
        try {
            // Coba ambil dari database lokal dulu
            $provinsi = Provinsi::orderBy('nama')->get();

            if ($provinsi->isEmpty()) {
                // Jika database kosong, coba dari API
                return $this->getProvinsiFromAPI();
            }

            return $provinsi;
        } catch (\Exception $e) {
            Log::error('Error getting provinsi: ' . $e->getMessage());
            return collect();
        }
    }

    public function getKabupatenKota($provinsiId)
    {
        try {
            $kabupatenKota = KabupatenKota::where('provinsi_id', $provinsiId)
                ->orderBy('nama')
                ->get();

            if ($kabupatenKota->isEmpty()) {
                return $this->getKabupatenKotaFromAPI($provinsiId);
            }

            return $kabupatenKota;
        } catch (\Exception $e) {
            Log::error('Error getting kabupaten/kota: ' . $e->getMessage());
            return collect();
        }
    }

    public function getKecamatan($kabupatenKotaId)
    {
        try {
            $kecamatan = Kecamatan::where('kabupaten_kota_id', $kabupatenKotaId)
                ->orderBy('nama')
                ->get();

            if ($kecamatan->isEmpty()) {
                return $this->getKecamatanFromAPI($kabupatenKotaId);
            }

            return $kecamatan;
        } catch (\Exception $e) {
            Log::error('Error getting kecamatan: ' . $e->getMessage());
            return collect();
        }
    }

    public function getDesa($kecamatanId)
    {
        try {
            $desa = Desa::where('kecamatan_id', $kecamatanId)
                ->orderBy('nama')
                ->get();

            if ($desa->isEmpty()) {
                return $this->getDesaFromAPI($kecamatanId);
            }

            return $desa;
        } catch (\Exception $e) {
            Log::error('Error getting desa: ' . $e->getMessage());
            return collect();
        }
    }

    private function getProvinsiFromAPI()
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/provinces.json');

            if ($response->successful()) {
                $data = $response->json();
                $provinsi = collect();

                foreach ($data as $item) {
                    $provinsi->push((object)[
                        'id' => $item['id'],
                        'kode' => $item['id'],
                        'nama' => $item['name']
                    ]);
                }

                return $provinsi;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching provinsi from API: ' . $e->getMessage());
        }

        return collect();
    }

    private function getKabupatenKotaFromAPI($provinsiId)
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . "/regencies/{$provinsiId}.json");

            if ($response->successful()) {
                $data = $response->json();
                $kabupatenKota = collect();

                foreach ($data as $item) {
                    $kabupatenKota->push((object)[
                        'id' => $item['id'],
                        'provinsi_id' => $provinsiId,
                        'kode' => $item['id'],
                        'nama' => $item['name']
                    ]);
                }

                return $kabupatenKota;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching kabupaten/kota from API: ' . $e->getMessage());
        }

        return collect();
    }

    private function getKecamatanFromAPI($kabupatenKotaId)
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . "/districts/{$kabupatenKotaId}.json");

            if ($response->successful()) {
                $data = $response->json();
                $kecamatan = collect();

                foreach ($data as $item) {
                    $kecamatan->push((object)[
                        'id' => $item['id'],
                        'kabupaten_kota_id' => $kabupatenKotaId,
                        'kode' => $item['id'],
                        'nama' => $item['name']
                    ]);
                }

                return $kecamatan;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching kecamatan from API: ' . $e->getMessage());
        }

        return collect();
    }

    private function getDesaFromAPI($kecamatanId)
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . "/villages/{$kecamatanId}.json");

            if ($response->successful()) {
                $data = $response->json();
                $desa = collect();

                foreach ($data as $item) {
                    $desa->push((object)[
                        'id' => $item['id'],
                        'kecamatan_id' => $kecamatanId,
                        'kode' => $item['id'],
                        'nama' => $item['name']
                    ]);
                }

                return $desa;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching desa from API: ' . $e->getMessage());
        }

        return collect();
    }

    // Method untuk sync data dari API ke database lokal
    public function syncAllWilayah()
    {
        try {
            // Sync Provinsi
            // $responseProvinsi = Http::timeout(30)->get($this->apiBaseUrl . '/provinces.json');
            // if ($responseProvinsi->successful()) {
            //     $provinsiData = $responseProvinsi->json();

            //     foreach ($provinsiData as $item) {
            //         Provinsi::updateOrCreate(
            //             ['kode' => $item['id']],
            //             ['nama' => $item['name']]
            //         );
            //     }

            //     Log::info('Provinsi synced successfully');
            // }

            // // Sync Kabupaten/Kota untuk setiap provinsi
            // $allProvinsi = Provinsi::all();
            // foreach ($allProvinsi as $provinsi) {
            //     $responseKabKota = Http::timeout(30)->get($this->apiBaseUrl . "/regencies/{$provinsi->kode}.json");

            //     if ($responseKabKota->successful()) {
            //         $kabKotaData = $responseKabKota->json();

            //         foreach ($kabKotaData as $item) {
            //             KabupatenKota::updateOrCreate(
            //                 ['kode' => $item['id']],
            //                 [
            //                     'provinsi_id' => $provinsi->id,
            //                     'nama' => $item['name']
            //                 ]
            //             );
            //         }
            //     }

            //     // Delay untuk menghindari rate limiting
            //     sleep(1);
            // }
            // Log::info('Kabupaten/Kota synced successfully');

            // Sync Kecamatan untuk setiap Kabupaten/Kota
            $allKabupatenKota = KabupatenKota::all();
            foreach ($allKabupatenKota as $kabupatenKota) {
                $responseKecamatan = Http::timeout(30)->get($this->apiBaseUrl . "/districts/{$kabupatenKota->kode}.json");

                if ($responseKecamatan->successful()) {
                    $kecamatanData = $responseKecamatan->json();

                    foreach ($kecamatanData as $item) {
                        Kecamatan::updateOrCreate(
                            ['kode' => $item['id']],
                            [
                                'kabupaten_kota_id' => $kabupatenKota->id,
                                'nama' => $item['name']
                            ]
                        );
                    }
                }

                // Delay untuk menghindari rate limiting
                sleep(1);
            }
            Log::info('Kecamatan synced successfully');

            // Sync Desa untuk setiap Kecamatan
            $allKecamatan = Kecamatan::all();
            foreach ($allKecamatan as $kecamatan) {
                $responseDesa = Http::timeout(30)->get($this->apiBaseUrl . "/villages/{$kecamatan->kode}.json");

                if ($responseDesa->successful()) {
                    $desaData = $responseDesa->json();

                    foreach ($desaData as $item) {
                        Desa::updateOrCreate(
                            ['kode' => $item['id']],
                            [
                                'kecamatan_id' => $kecamatan->id,
                                'nama' => $item['name']
                            ]
                        );
                    }
                }

                // Delay untuk menghindari rate limiting
                sleep(1);
            }
            Log::info('Desa synced successfully');

            Log::info('All wilayah synced successfully');
            return true;

        } catch (\Exception $e) {
            Log::error('Error syncing wilayah: ' . $e->getMessage());
            return false;
        }
    }
}
