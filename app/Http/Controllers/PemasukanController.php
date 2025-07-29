<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Models\Ledger;
use App\Models\Pemasukan;
use App\Helpers\LogPretty;
use App\Models\PosPemasukan;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use App\Models\TabunganSiswa;
use App\Models\PemasukanDetail;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Models\PemasukanPembayaran;
use App\Models\SIAKAD\SiswaKelas;
use Illuminate\Support\Facades\Log;
use App\Services\TagihanSiswaService;
use Illuminate\Support\Facades\Validator;

class PemasukanController extends Controller
{
    public function index(Request $request) {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Tahun Ajaran',
            'No Transaksi',
            'Siswa',
            'Kelas',
            'Tanggal',
            'Total',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $bulanSekarang = Bulan::where('angka_bulan', date('n'))->first();

        $pemasukan = Pemasukan::with([
            'siswa_kelas.siswa',
            'siswa_kelas.kelas',
            'tahun_ajaran',
        ])
        ->when($request->angka_bulan, function($query) use ($request) {
            $query->whereMonth('tanggal', $request->angka_bulan);
        }, function($query) {
            $query->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'));
        })
        ->orderBy('tanggal', 'desc')
        ->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [ null, null, null, null, null, null, null, ['orderable' => false]]
        ];

        $no = 1;

        foreach($pemasukan as $item) {
            $siswa = $item->siswa_kelas->siswa->nama_lengkap ?? '-';
            $kelas = $item->siswa_kelas->kelas->nama_kelas ?? '-';
            $tahun_ajaran = $item->tahun_ajaran->nama_tahun_ajaran ?? '-';

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->no_transaksi.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('pemasukan.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $tahun_ajaran,
                $item->no_transaksi ?? '-',
                $siswa,
                $kelas,
                $item->tanggal ? date('d-m-Y', strtotime($item->tanggal)) : '-',
                'Rp '.number_format($item->total ?? 0, 0, ',', '.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }
        $data['bulans'] = Bulan::all();
        $data['bulanSekarang'] = $bulanSekarang;

        return view('pages.pemasukan.index', $data);
    }

    public function form(Request $request){

        $data['data'] = ($request->id) ? Pemasukan::
        // with([
        //     'siswa_kelas.siswa',
        //     'siswa_kelas.kelas',
        //     'bulan',
        //     'tahun_ajaran',
        //     'pemasukan_detail.pos_pemasukan',
        // ])->
        find($request->id) : [];

        // $data['pos_pemasukan'] = PosPemasukan::where('wajib',true)->get();
        // $data['pos_pemasukan_insidential'] = PosPemasukan::where('wajib',false)->where('nominal_valid','>',0)->get();
        $data['bulans'] = Bulan::all();
        $data['tahunAjaranAktif'] = TahunAjaran::where('is_aktif', true)->first();


        return view('pages.pemasukan.form',$data);
    }

    public function getTagihanSiswa(Request $request, $siswaKelasId)
    {
        $currentYear = TahunAjaran::where('is_aktif',true)->first();

        return response()->json([
            'tagihan' => TagihanSiswaService::getTagihanSiswaBySiswaKelasId($siswaKelasId, $currentYear, $request->jenis_kelamin, $request->jenjang_id),
            'current_month' => Bulan::where('angka_bulan',date('n'))->first()
        ]);
    }

    public function store(Request $request){
        $rules = [
            'siswa_kelas_id' => 'required|exists:mysql_siakad.siswa_kelas,id',
            'item_transaksi' => 'required|array',
            'item_transaksi.bulanan' => 'array',
            'item_transaksi.non_bulanan' => 'array',
            'item_transaksi.lainnya' => 'array',
        ];
        $messages = [
            'siswa_kelas_id.required' => 'Siswa Kelas harus dipilih',
            'siswa_kelas_id.exists' => 'Siswa Kelas tidak ditemukan',
            'item_transaksi.required' => 'Transaksi harus diisi',
            'item_transaksi.array' => 'Transaksi harus berupa array',
        ];
        $attributes = [
            'siswa_kelas_id' => 'Siswa Kelas',
            'item_transaksi.bulanan' => 'Transaksi Bulanan',
            'item_transaksi.non_bulanan' => 'Transaksi Non Bulanan',
            'item_transaksi.lainnya' => 'Transaksi Lainnya',
        ];

        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            // 1. Create Pemasukan
            $pemasukan = Pemasukan::create([
                'no_transaksi' => Pemasukan::generateNoTransaksi(),
                'siswa_kelas_id' => $request->siswa_kelas_id,
                'tahun_ajaran_id' => TahunAjaran::where('is_aktif', true)->first()->id,
                'tanggal' => now(),
                'total' => 0
            ]);
            $siswakelas = SiswaKelas::find($request->siswa_kelas_id);
            $siswaid = $siswakelas->siswa_id;

            $totalTagihan = 0;
            $totalDibayar = 0;
            $itemTransaksi = $request->item_transaksi;

            // 2. Process bulanan items
            if (!empty($itemTransaksi['bulanan'])) {
                foreach ($itemTransaksi['bulanan'] as $item) {
                    $diskon = 0;
                    $diskon_persen = 0;
                    $diskon_nominal = 0;
                    if(isset($item['diskon_persen']) && isset($item['diskon_nominal'])){
                        if($item['diskon_persen'] > 0){
                            $diskon_persen = $item['diskon_persen'];
                            $diskon = $diskon_persen * $item['dibayar'] / 100;
                        }elseif($item['diskon_nominal'] > 0){
                            $diskon_nominal = $item['diskon_nominal'];
                            $diskon = $diskon_nominal;
                        }
                    }
                    $subtotal = $item['dibayar'] - $diskon;
                    // Create pemasukan detail
                    $pemasukan_detail = PemasukanDetail::create([
                        'pemasukan_id' => $pemasukan->id,
                        'tagihan_siswa_id' => $item['id'],
                        'pos_pemasukan_id' => null,
                        'nominal' => $item['dibayar'],
                        'diskon_persen' => $diskon_persen,
                        'diskon_nominal' => $diskon_nominal,
                        'subtotal' => $subtotal
                    ]);

                    // Update tagihan siswa status
                    $tagihanSiswa = TagihanSiswa::find($item['id']);
                    if ($item['dibayar'] >= $item['sisa']) {
                        $tagihanSiswa->status = 'lunas';
                    } else if ($item['dibayar'] > 0) {
                        $tagihanSiswa->status = 'belum_lunas';
                    }
                    $tagihanSiswa->save();

                    // Create pemasukan pembayaran
                    PemasukanPembayaran::create([
                        'pemasukan_id' => $pemasukan->id,
                        'pemasukan_detail_id' => $pemasukan_detail->id,
                        'tanggal' => now(),
                        'nominal' => $item['dibayar'],
                        'metode' => 'tunai',
                    ]);

                    $totalTagihan += $subtotal;
                    $totalDibayar += $item['dibayar'];
                }
            }

            // 3. Process non bulanan items
            if (!empty($itemTransaksi['non_bulanan'])) {
                foreach ($itemTransaksi['non_bulanan'] as $item) {
                    $diskon = 0;
                    $diskon_persen = 0;
                    $diskon_nominal = 0;
                    if(isset($item['diskon_persen']) && isset($item['diskon_nominal'])){
                        if($item['diskon_persen'] > 0){
                            $diskon_persen = $item['diskon_persen'];
                            $diskon = $diskon_persen * $item['dibayar'] / 100;
                        }elseif($item['diskon_nominal'] > 0){
                            $diskon_nominal = $item['diskon_nominal'];
                            $diskon = $diskon_nominal;
                        }
                    }
                    $subtotal = $item['dibayar'] - $diskon;
                    // Create pemasukan detail
                    $pemasukan_detail = PemasukanDetail::create([
                        'pemasukan_id' => $pemasukan->id,
                        'tagihan_siswa_id' => $item['id'],
                        'pos_pemasukan_id' => null,
                        'nominal' => $item['dibayar'],
                        'diskon_persen' => $diskon_persen,
                        'diskon_nominal' => $diskon_nominal,
                        'subtotal' => $subtotal
                    ]);

                    // Update tagihan siswa status
                    $tagihanSiswa = TagihanSiswa::find($item['id']);
                    if ($item['dibayar'] >= $item['sisa']) {
                        $tagihanSiswa->status = 'lunas';
                    } else if ($item['dibayar'] > 0) {
                        $tagihanSiswa->status = 'belum_lunas';
                    }
                    $tagihanSiswa->save();

                    // Create pemasukan pembayaran
                    PemasukanPembayaran::create([
                        'pemasukan_id' => $pemasukan->id,
                        'pemasukan_detail_id' => $pemasukan_detail->id,
                        'tanggal' => now(),
                        'nominal' => $item['dibayar'],
                        'metode' => 'tunai',
                    ]);

                    $totalTagihan += $subtotal;
                    $totalDibayar += $item['dibayar'];
                }
            }

            // 4. Process lainnya items
            if (!empty($itemTransaksi['lainnya'])) {
                foreach ($itemTransaksi['lainnya'] as $item) {
                    $diskon = 0;
                    $diskon_persen = 0;
                    $diskon_nominal = 0;
                    if(isset($item['diskon_persen']) && isset($item['diskon_nominal'])){
                        if($item['diskon_persen'] > 0){
                            $diskon_persen = $item['diskon_persen'];
                            $diskon = $diskon_persen * $item['dibayar'] / 100;
                        }elseif($item['diskon_nominal'] > 0){
                            $diskon_nominal = $item['diskon_nominal'];
                            $diskon = $diskon_nominal;
                        }
                    }
                    $subtotal = $item['dibayar'] - $diskon;
                    // Create pemasukan detail
                    $pemasukan_detail = PemasukanDetail::create([
                        'pemasukan_id' => $pemasukan->id,
                        'tagihan_siswa_id' => null,
                        'pos_pemasukan_id' => $item['pos_id'],
                        'nominal' => $item['dibayar'],
                        'diskon_persen' => $diskon_persen,
                        'diskon_nominal' => $diskon_nominal,
                        'subtotal' => $subtotal
                    ]);

                    // Create pemasukan pembayaran
                    PemasukanPembayaran::create([
                        'pemasukan_id' => $pemasukan->id,
                        'pemasukan_detail_id' => $pemasukan_detail->id,
                        'tanggal' => now(),
                        'nominal' => $item['dibayar'],
                        'metode' => 'tunai',
                    ]);

                    // create tabungan siswa if istabungan is true
                    if (isset($item['istabungan']) && $item['istabungan']) {
                        TabunganSiswa::create([
                            'siswa_id' => $siswaid,
                            'nominal' => $item['dibayar'],
                            'tanggal' => now(),
                        ]);
                    }

                    $totalTagihan += $subtotal;
                    $totalDibayar += $item['dibayar'];
                }
            }

            // 5. Update pemasukan total
            $pemasukan->total = $totalTagihan;
            $pemasukan->save();

            // 6. Create ledger entry
            Ledger::create([
                'sumber_tabel' => 'pemasukan',
                'referensi_id' => $pemasukan->id,
                'tipe' => 'in',
                'jenis_akun' => 'pendapatan',
                'trx_date' => now(),
                'keterangan' => 'Pembayaran siswa',
                'debit' => $totalDibayar,
                'kredit' => 0
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan',
                'data' => $pemasukan
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            LogPretty::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data, kesalahan pada sistem',
            ]);
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try {
            $pemasukan = Pemasukan::find($id);
            PemasukanDetail::where('pemasukan_id',$pemasukan->id)->delete();
            PemasukanPembayaran::where('pemasukan_id',$pemasukan->id)->delete();
            Ledger::where([
                'sumber_tabel' => 'pemasukan',
                'referensi_id' => $pemasukan->id
            ])->delete();
            $pemasukan->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data, kesalahan pada sistem',
            ]);
        }
    }
}
