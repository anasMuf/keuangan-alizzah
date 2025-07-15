<?php

namespace App\Imports;

use App\Helpers\LogPretty;
use Carbon\Carbon;
use App\Models\SIAKAD\Kelas;
use App\Models\SIAKAD\Siswa;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\Log;
use App\Services\TagihanSiswaService;
use App\Services\WilayahLookupService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnError
{
    use Importable, SkipsErrors;

    // protected $wilayahLookupService;
    protected $importErrors = [];

    // public function __construct()
    // {
    //     $this->wilayahLookupService = new WilayahLookupService();
    // }

    public function model(array $row)
    {
        LogPretty::info('Memproses baris: ' . json_encode($row, JSON_PRETTY_PRINT));
        // Parsing tanggal lahir
        // Log::info(json_encode($row['tanggal_lahir'],JSON_PRETTY_PRINT));
        $tanggalLahir = $this->parseTanggalLahir($row['tanggal_lahir'] ?? null);

        // // Lookup wilayah
        // $provinsi = $this->wilayahLookupService->findProvinsiByName($row['provinsi'] ?? null);
        // $kabupatenKota = $this->wilayahLookupService->findKabupatenKotaByName(
        //     $row['kabupaten_kota'] ?? null,
        //     $provinsi ? $provinsi->id : null
        // );
        // $kecamatan = $this->wilayahLookupService->findKecamatanByName(
        //     $row['kecamatan'] ?? null,
        //     $kabupatenKota ? $kabupatenKota->id : null
        // );
        // $desa = $this->wilayahLookupService->findDesaByName(
        //     $row['desa'] ?? null,
        //     $kecamatan ? $kecamatan->id : null
        // );

        // // Validasi chain wilayah
        // $wilayahErrors = $this->wilayahLookupService->validateWilayahChain($provinsi, $kabupatenKota, $kecamatan, $desa);

        // if (!empty($wilayahErrors)) {
        //     // Log error untuk reporting
        //     $this->importErrors[] = [
        //         'row' => $row,
        //         'errors' => $wilayahErrors
        //     ];

        //     // Skip row ini jika ada error wilayah
        //     return null;
        // }

        $jenis_kelamin = $row['jenis_kelamin'];
        if($jenis_kelamin == 'L' || $jenis_kelamin == 'Laki-laki'){
            $jenis_kelamin = 'Laki-laki';
        }elseif($jenis_kelamin == 'P' || $jenis_kelamin == 'Perempuan'){
            $jenis_kelamin = 'Perempuan';
        }else{
            $jenis_kelamin = null;
        }

        $siswa = new Siswa();
        // Data siswa basic
        $siswa->nama_lengkap = $row['nama_lengkap'] ?? null;
        $siswa->nama_panggilan = $row['nama_panggilan'] ?? null;
        $siswa->nik = $row['nik'] ?? null;
        $siswa->jenis_kelamin = $jenis_kelamin;
        $siswa->tempat_lahir = $row['tempat_lahir'] ?? null;
        $siswa->tanggal_lahir = $tanggalLahir;

        // Data alamat
        $siswa->alamat_lengkap = $row['alamat_lengkap'] ?? null;
        $siswa->desa = $row['desa'] ?? null;
        $siswa->kecamatan = $row['kecamatan'] ?? null;
        $siswa->kabupaten_kota = $row['kabupaten_kota'] ?? null;
        $siswa->provinsi = $row['provinsi'] ?? null;

        $siswa->agama = $row['agama'] ?? null;
        $siswa->kewarganegaraan = $row['kewarganegaraan'] ?? 'Indonesia';
        $siswa->anak_keberapa = $row['anak_keberapa'] ?? null;
        $siswa->jumlah_saudara_kandung = $row['jumlah_saudara_kandung'] ?? null;
        $siswa->jumlah_saudara_tiri = $row['jumlah_saudara_tiri'] ?? null;
        $siswa->jumlah_saudara_angkat = $row['jumlah_saudara_angkat'] ?? null;
        $siswa->status_orangtua = $row['status_orangtua'] ?? null;
        $siswa->bahasa_seharihari = $row['bahasa_seharihari'] ?? null;
        $siswa->golongan_darah = $row['golongan_darah'] ?? null;
        $siswa->riwayat_penyakit = $row['riwayat_penyakit'] ?? null;
        $siswa->riwayat_imunisasi = $row['riwayat_imunisasi'] ?? null;
        $siswa->ciri_khusus = $row['ciri_khusus'] ?? null;
        $siswa->cita_cita = $row['cita_cita'] ?? null;

        $siswa->save();
        // LogPretty::info('Siswa berhasil disimpan: ' . $siswa->nama_lengkap);

        $namaKelas = $row['kelas'];
        $tahunAjaran = TahunAjaran::where('is_aktif',true)->first();
        if (!empty($namaKelas) && $tahunAjaran) {
            // LogPretty::info('Mencari kelas untuk siswa: ' . $siswa->nama_lengkap . ' dengan nama kelas: ' . $namaKelas);
            $kelas = Kelas::whereRaw("LOWER(nama_kelas) LIKE ?", ['%' . strtolower($namaKelas) . '%'])->first();

            if ($kelas) {
                // LogPretty::info('Kelas ditemukan: ' . $kelas->nama_kelas);
                // Nonaktifkan semua data SiswaKelas sebelumnya
                SiswaKelas::where('siswa_id', $siswa->id)->update(['status' => 'nonaktif']);

                // Tambahkan data baru
                $siswaKelas = new SiswaKelas();
                $siswaKelas->tahun_ajaran_id = $tahunAjaran->id;
                $siswaKelas->siswa_id = $siswa->id;
                $siswaKelas->kelas_id = $kelas->id;
                $siswaKelas->asal_sekolah = $row['asal_sekolah'] ?? null;
                $siswaKelas->status = 'aktif';
                $siswaKelas->save();

                // Buat tagihan siswa
                // $tagihanSiswaService = new TagihanSiswaService();
                // $dataTagihan = [
                //     'tahun_ajaran_id' => $tahunAjaran->id,
                //     'siswa_kelas' => SiswaKelas::with('siswa.siswa_dispensasi')->find($siswaKelas->id)->toArray(),
                //     'siswa_dispensasi' => $siswaKelas['siswa']['siswa_dispensasi'] ?? [],
                // ];
                // $resultTagihan = $tagihanSiswaService->create($dataTagihan);
                // LogPretty::info('Hasil pembuatan tagihan siswa: ' . json_encode($resultTagihan, JSON_PRETTY_PRINT));
                // if(!$resultTagihan['success']) {
                    // LogPretty::error('Gagal membuat tagihan siswa: ' . $resultTagihan['message']);
                //     return [];
                // }
            }else{
                // LogPretty::error('Kelas tidak ditemukan: ' . $namaKelas);
                // Simpan error jika kelas tidak ditemukan
                $this->importErrors[] = [
                    'row' => $row,
                    'errors' => ['Kelas tidak ditemukan: ' . $namaKelas]
                ];
                return [];
            }
        }else{
            // LogPretty::error('Nama kelas atau tahun ajaran tidak ditemukan untuk siswa: ' . $siswa->nama_lengkap);
            // Simpan error jika nama kelas atau tahun ajaran tidak ditemukan
            $this->importErrors[] = [
                'row' => $row,
                'errors' => ['Nama kelas atau tahun ajaran tidak ditemukan']
            ];
            return [];
        }

        return $siswa;
    }

    private function parseTanggalLahir($tanggalLahir)
    {
        if (empty($tanggalLahir)) {
            return null;
        }

        try {
            if (is_numeric($tanggalLahir)) {
                // Excel date format
                return Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($tanggalLahir - 25569) * 86400));
            } else {
                // String date
                return Carbon::parse($tanggalLahir);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:16|unique:siswa,nik',
            'jenis_kelamin' => 'nullable|in:L,Laki-laki,P,Perempuan',
            'tanggal_lahir' => 'nullable',
            'alamat_lengkap' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'kabupaten_kota' => 'nullable|string',
            'kecamatan' => 'nullable|string',
            'desa' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'nik.max' => 'NIK maksimal 16 karakter',
            'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
        ];
    }

    public function chunkSize(): int
    {
        return 500; // Reduce chunk size karena ada lookup wilayah
    }

    public function getImportErrors()
    {
        return $this->importErrors;
    }
}
