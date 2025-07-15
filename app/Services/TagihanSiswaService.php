<?php

namespace App\Services;

use App\Helpers\LogPretty;
use App\Models\Bulan;
use App\Models\PosPemasukan;
use App\Models\TagihanSiswa;
use Illuminate\Support\Facades\Log;

class TagihanSiswaService
{
    public function create(array $data) : array
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
            foreach($posPemasukan as $itemPosPemasukan) {
                $nominal = $itemPosPemasukan->nominal_valid;
                $existingTagihan = TagihanSiswa::where('siswa_kelas_id', $siswa_kelas_id)
                    ->where('pos_pemasukan_id', $itemPosPemasukan->id)
                    ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
                    ->first();
                // // apakah kelas siswa ini biaya_awal true
                $kelasBiayaAwal = true;
                // // jika biaya awal, maka buat tagihan
                if($itemPosPemasukan->id == 1) {
                    $existingTagihan = null; // set existingTagihan ke null agar tagihan dibuat
                    $kelasBiayaAwal = $data['siswa_kelas']['kelas']['biaya_awal'];
                };
                if(!$existingTagihan && $kelasBiayaAwal) {
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
                            $totalNominal = $nominal - ($nominal * $persentase_overide / 100);
                        }elseif($nominal_overide > 0) {
                            $totalNominal = $nominal - $nominal_overide;
                        }else{
                            $totalNominal = $nominal;
                        }
                        $bulanId = null;
                        // cek jenjang_pos_pemasukan->jenjang_id == siswa_kelas->kelas->jenjang_id
                        $jenjangIdSiswa = $data['siswa_kelas']['kelas']['jenjang_id'];
                        $jenjangIdPosPemasukan = $itemJenjangPosPemasukan->jenjang_id;
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
                            }else{
                                // jika pembayaran sekali
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
                                    'jumlah_harus_dibayar' => 1,
                                    'status' => 'belum_bayar',
                                    'keterangan' => '',
                                ]);
                            }
                        }
                    }
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
