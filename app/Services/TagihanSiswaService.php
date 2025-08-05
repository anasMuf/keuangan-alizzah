<?php

namespace App\Services;

use App\Helpers\LogPretty;
use App\Models\Bulan;
use App\Models\PosPemasukan;
use App\Models\TagihanSiswa;
use Illuminate\Support\Facades\Log;

class TagihanSiswaService
{
    public function create(array $data, $customPos = null, $nominalBerbeda = 0) : array
    {
        //cek isi pos_pemasukan ada atau tidak
        if(PosPemasukan::whereIn('pembayaran',['sekali','harian','mingguan','bulanan','tahunan'])->count() > 0) {
            //jika ada, buat tagihan siswa di tabel tagihan_siswa
            //ambil siswa_kelas_id dari data yang dikirim
            $siswa_kelas_id = $data['siswa_kelas']['id'];
            $siswa_dispensasi_id = $data['siswa_dispensasi']['id'] ?? null;
            $persentase_overide = $data['siswa_dispensasi']['persentase_overide'] ?? 0;
            $nominal_overide = $data['siswa_dispensasi']['nominal_overide'] ?? 0;
            $totalNominal = 0;
            //ambil semua pos_pemasukan yang aktif
            $posPemasukan = PosPemasukan::with('jenjang_pos_pemasukan.jenjang_pos_pemasukan_detail')->where(['wajib' => true, 'optional' => false])->get();
            if($customPos) {
                $posPemasukan = PosPemasukan::with('jenjang_pos_pemasukan.jenjang_pos_pemasukan_detail')->where('id', $customPos)->get();
            }
            if($posPemasukan->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada pos pemasukan yang dimaksud.',
                ];
            }
            foreach($posPemasukan as $itemPosPemasukan) {
                $nominal = $nominalBerbeda == 0 ? $itemPosPemasukan->nominal_valid : $nominalBerbeda;
                LogPretty::info('nominal', $nominal);
                $existingTagihan = TagihanSiswa::where('siswa_kelas_id', $siswa_kelas_id)
                    ->where('pos_pemasukan_id', $itemPosPemasukan->id)
                    ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
                    ->orderBy('jumlah_harus_dibayar', 'desc')
                    ->first();
                $jumlah_harus_dibayar_terakhir = $existingTagihan ? $existingTagihan->jumlah_harus_dibayar : 0;
                // // apakah kelas siswa ini biaya_awal true
                $kelasBiayaAwal = true;
                // // jika biaya awal, maka buat tagihan
                if($itemPosPemasukan->id == 1 && $posPemasukan->isEmpty()) {
                    $kelasBiayaAwal = $data['siswa_kelas']['kelas']['biaya_awal'];
                    // if($kelasBiayaAwal) {
                    //     $existingTagihan = null; // set existingTagihan ke null agar tagihan dibuat
                    // }
                };
                // jika itemPosPemasukan->wajib == true, dan itemPosPemasukan->optional == true, maka buat tagihan siswa
                if($itemPosPemasukan->wajib && $itemPosPemasukan->optional) {
                    LogPretty::info('Cek pos pemasukan wajib dan optional', [
                        'wajib' => $itemPosPemasukan->wajib,
                        'optional' => $itemPosPemasukan->optional,
                    ]);
                    $existingTagihan = null; // set existingTagihan ke null agar tagihan dibuat
                }
                LogPretty::info('Cek tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan, [
                    'kelas_biaya_awal' => $kelasBiayaAwal,
                    'existing_tagihan' => $existingTagihan,
                ]);
                if(!$existingTagihan && $kelasBiayaAwal) {
                    LogPretty::info('Membuat tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan . ' dengan id: ' . $itemPosPemasukan->id);
                    foreach($itemPosPemasukan->jenjang_pos_pemasukan as $itemJenjangPosPemasukan) {
                        if($itemJenjangPosPemasukan->jenjang_pos_pemasukan_detail){
                            foreach($itemJenjangPosPemasukan->jenjang_pos_pemasukan_detail as $itemJenjangPosPemasukanDetail){
                                $jkSiswa = $data['siswa_kelas']['siswa']['jenis_kelamin'];
                                $jkJenjangPosPemasukanDetail = $itemJenjangPosPemasukanDetail->jenis_kelamin;
                                if($jkSiswa == $jkJenjangPosPemasukanDetail){
                                    $nominal = $itemJenjangPosPemasukanDetail->nominal;
                                }
                            }
                        }
                        if($persentase_overide > 0) {
                            $totalNominal = $nominal - ($nominal * $persentase_overide);
                        }elseif($nominal_overide > 0) {
                            $totalNominal = $nominal - $nominal_overide;
                        }else{
                            $totalNominal = $nominal;
                        }
                        $bulanId = null;
                        // cek jenjang_pos_pemasukan->jenjang_id == siswa_kelas->kelas->jenjang_id
                        $jenjangIdSiswa = $data['siswa_kelas']['kelas']['jenjang_id'];
                        $jenjangIdPosPemasukan = $itemJenjangPosPemasukan->jenjang_id;
                        LogPretty::info('Cek jenjang pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan, [
                            'jenjang_id_siswa' => $jenjangIdSiswa,
                            'jenjang_id_pos_pemasukan' => $jenjangIdPosPemasukan,
                        ]);
                        if($jenjangIdSiswa == $jenjangIdPosPemasukan) {
                            // cek pos_pemasukan yang attribut pembayaran == 'bulanan'
                            if($itemPosPemasukan->pembayaran == 'bulanan') {
                                // jika pembayaran bulanan, buat tagihan untuk setiap bulan
                                $bulan = Bulan::all();
                                if($bulan->isEmpty()) {
                                    return [
                                        'success' => false,
                                        'message' => 'Tidak ada bulan yang tersedia.',
                                    ];
                                }
                                foreach($bulan as $bulanItem) {
                                    $bulanId = $bulanItem->id;
                                    $tanggal_tagihan = strtotime(date('Y') . '-' . $bulanItem->angka_bulan . '-01');
                                    $tanggal_jatuh_tempo = strtotime(date('Y') . '-' . $bulanItem->angka_bulan . '-25');
                                    $tagihanSiswaService = new TagihanSiswa();
                                    $tagihanSiswaService->create([
                                        'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                                        'bulan_id' => $bulanId,
                                        'siswa_kelas_id' => $siswa_kelas_id,
                                        'pos_pemasukan_id' => $itemPosPemasukan->id,
                                        'tanggal_tagihan' => null,
                                        'tanggal_jatuh_tempo' => null,
                                        'siswa_dispensasi_id' => $siswa_dispensasi_id,
                                        'nominal_awal' => $nominal,
                                        'diskon_persen' => $persentase_overide,
                                        'diskon_nominal' => $nominal_overide,
                                        'nominal' => $totalNominal,
                                        'status' => 'belum_bayar',
                                        'jumlah_harus_dibayar' => 1,
                                        'keterangan' => '',
                                    ]);
                                }
                                LogPretty::info('Membuat tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan);
                            }else{
                                // jika pembayaran sekali
                                $jumlah_harus_dibayar = $jumlah_harus_dibayar_terakhir;
                                if($itemPosPemasukan->pembayaran == 'harian') {
                                    $jumlah_harus_dibayar++; // jika harian, maka 30 hari
                                }
                                LogPretty::info('Membuat tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan . ' dengan id: ' . $itemPosPemasukan->id);
                                $tagihanSiswaService = new TagihanSiswa();
                                $tagihanSiswaService->create([
                                    'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                                    'bulan_id' => $bulanId,
                                    'siswa_kelas_id' => $siswa_kelas_id,
                                    'pos_pemasukan_id' => $itemPosPemasukan->id,
                                    'tanggal_tagihan' => null,
                                    'tanggal_jatuh_tempo' => null,
                                    'siswa_dispensasi_id' => $siswa_dispensasi_id,
                                    'nominal_awal' => $nominal,
                                    'diskon_persen' => $persentase_overide,
                                    'diskon_nominal' => $nominal_overide,
                                    'nominal' => $totalNominal,
                                    'jumlah_harus_dibayar' => $jumlah_harus_dibayar,
                                    'status' => 'belum_bayar',
                                    'keterangan' => '',
                                ]);
                                Log::info('Kelas biaya awal: ' . $kelasBiayaAwal);
                                LogPretty::info('Berhasil Membuat tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan);
                            }
                        }else {
                            continue;
                            LogPretty::info('Gagal, Jenjang pos pemasukan tidak sesuai dengan jenjang siswa kelas, melewati pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan);
                            // return [
                            //     'success' => false,
                            //     'message' => 'Tagihan siswa gagal dibuat. pos pemasukan ' . $itemPosPemasukan->nama_pos_pemasukan . ' tidak sesuai dengan jenjang siswa kelas.',
                            // ];
                        }
                    }
                }else{
                    //update tagihan siswa jika sudah ada
                    LogPretty::info('Tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan . ' sudah ada, update tagihan siswa');
                    // $tagihanSiswaService = TagihanSiswa::where('siswa_kelas_id', $siswa_kelas_id)
                    //     ->where('pos_pemasukan_id', $itemPosPemasukan->id)
                    //     ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
                    //     ->orderBy('jumlah_harus_dibayar', 'desc')
                    //     ->first();

                    // $tagihanSiswaService->update([
                    //     'siswa_dispensasi_id' => $siswa_dispensasi_id,
                    //     'nominal_awal' => $nominal,
                    //     'diskon_persen' => $persentase_overide,
                    //     'diskon_nominal' => $nominal_overide,
                    //     'nominal' => $totalNominal,
                    // ]);
                    // LogPretty::info('Berhasil update tagihan siswa kelas id ' . $siswa_kelas_id . ' untuk pos pemasukan: ' . $itemPosPemasukan->nama_pos_pemasukan);
                }
            }
            return [
                'success' => true,
                'message' => 'Tagihan siswa berhasil dibuat.',
            ];
        }
        //jika tidak ada lewati
        return [
            'success' => false,
            'message' => 'Tidak ada pos pemasukan yang aktif.',
        ];
    }

    public static function getTagihanSiswaBySiswaKelasId($siswaKelasId,$currentYear,$jenisKelamin,$jenjangId)
    {
        $bulan = Bulan::all();
        $pembayaran = ['sekali','harian','mingguan','bulanan','tahunan'];
        $dataTagihan = [];
        foreach($pembayaran as $item){
            $tagihan = TagihanSiswa::with([
                'tahun_ajaran', 'bulan', 'siswa_kelas',
                'pos_pemasukan.jenjang_pos_pemasukan' => function($q) use ($jenjangId){
                    $q->where('jenjang_id',$jenjangId);
                },
                'pos_pemasukan.jenjang_pos_pemasukan.jenjang_pos_pemasukan_detail' => function($q) use ($jenisKelamin){
                    $q->where('jenis_kelamin',$jenisKelamin);
                },
                'pemasukan_detail.pemasukan_pembayaran'
            ])
            ->where('siswa_kelas_id', $siswaKelasId)
            ->where('tahun_ajaran_id', $currentYear->id)
            ->whereHas('pos_pemasukan',fn($q)=>$q->where('pembayaran',$item))
            ->orderBy('bulan_id')
            ->get();
            if(!empty($tagihan)){
                if($item == 'bulanan'){
                    foreach($bulan as $itemBulan){
                        $statusArr = [];
                        foreach($tagihan as $itemTagihan){
                            if($itemBulan->id == $itemTagihan->bulan_id){
                                $statusArr[] = $itemTagihan->status;

                                $dataTagihan[$item][$itemBulan->id]['details'][$itemTagihan->pos_pemasukan->nama_pos_pemasukan] = $itemTagihan;
                            }
                        }

                        // jika statusArr memiliki 1 belum_lunas atau 1 lunas, maka statusnya adalah belum_lunas
                        // jika statusArr hanya memiliki belum_bayar, maka statusnya adalah belum_bayar
                        // jika statusArr hanya memiliki lunas, maka statusnya adalah lunas
                        if(in_array('belum_lunas', $statusArr)){
                            $status = 'belum_lunas';
                        }elseif(in_array('belum_bayar', $statusArr)){
                            $status = 'belum_bayar';
                        }elseif(in_array('lunas', $statusArr)){
                            $status = 'lunas';
                        }else{
                            $status = 'belum_bayar';
                        }
                        // LogPretty::info('Status Tagihan: '.$status, $statusArr);
                        // $dataTagihan[$item][$itemBulan->id]['statuses'] = $statusArr;
                        $dataTagihan[$item][$itemBulan->id]['status'] = $status;
                    }
                }else{
                    $dataTagihan['non_bulan'][$item] = $tagihan;
                }
            }
        }
        return $dataTagihan;
    }

    public function updateStatus(TagihanSiswa $tagihanSiswa, array $data)
    {
        $tagihanSiswa->update($data);
        return $tagihanSiswa;
    }
}
