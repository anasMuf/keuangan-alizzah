<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Pemasukan;
use App\Helpers\LogPretty;
use App\Models\Pengeluaran;
use App\Models\PosPemasukan;
use App\Models\SIAKAD\Siswa;
use Illuminate\Http\Request;
use App\Models\TabunganSiswa;
use App\Models\PosPengeluaran;
use App\Models\PemasukanDetail;
use App\Models\PengeluaranDetail;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Models\PemasukanPembayaran;
use App\Models\PengeluaranPembayaran;
use Illuminate\Support\Facades\Validator;

class TabunganSiswaController extends Controller
{
    public function index()
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Siswa',
            'Saldo',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $siswa = Siswa::with('tabungan_siswa')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                ['orderable' => false]
            ]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $saldoTabungan = 0;
        $no = 1;

        foreach($siswa as $item){
            if($item->tabungan_siswa->isEmpty()){
                continue;
            }
            // $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_tabungan_siswa.'`)">
            //     <i class="fa fa-lg fa-fw fa-trash"></i>
            // </button>';
            $btnDetails = '<a href="'.route('tabungan_siswa.detail', ['siswa_id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $tabunganHelper = new \App\Services\TabunganSiswaService();
            $saldoTabungan = $tabunganHelper->tambahSaldoAkhir($item->tabungan_siswa);
            $countSaldo = $saldoTabungan->count();

            $data['config']['data'][] = [
                $no++,
                $item->nama_lengkap,
                'Rp '.number_format($saldoTabungan[$countSaldo - 1]->saldo_akhir, 0, ',', '.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.tabungan_siswa.index', $data);
    }

    public function detail($siswa_id)
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Tanggal',
            'Keterangan',
            'Debit',
            'Kredit',
        ];

        $tabungan_siswa = TabunganSiswa::with('siswa')->where('siswa_id', $siswa_id)->orderBy('tanggal','asc')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                null,
                null,
            ]
        ];

        $no = 1;

        foreach($tabungan_siswa as $item){

            $data['config']['data'][] = [
                $no++,
                $item->tanggal,
                $item->keterangan,
                ($item->debit > 0) ? 'Rp '.number_format($item->debit, 0, ',', '.') : '-',
                ($item->kredit > 0) ? 'Rp '.number_format($item->kredit, 0, ',', '.') : '-'
            ];
        }

        $data['siswaKelas'] = SiswaKelas::with('siswa.tabungan_siswa','kelas')->where([
            'tahun_ajaran_id' => TahunAjaran::where('is_aktif', true)->first()->id,
            'siswa_id' => $siswa_id,
            'status' => 'aktif'
        ])->first();

        $tabunganSiswa = $data['siswaKelas']->siswa->tabungan_siswa ?? collect();

        $tabunganHelper = new \App\Services\TabunganSiswaService();
        $saldoTabungan = $tabunganHelper->tambahSaldoAkhir($tabunganSiswa);
        $countSaldo = $saldoTabungan->count();

        $data['totalSaldo'] = $saldoTabungan ? $saldoTabungan[$countSaldo - 1]->saldo_akhir : 0;

        return view('pages.tabungan_siswa.detail', $data);
    }

    public function form(Request $request, $siswa_id)
    {
        $tahunAjaran = TahunAjaran::where('is_aktif',true)->first();
        $data['siswaKelas'] = SiswaKelas::with('siswa.tabungan_siswa','kelas')->where('siswa_id', $siswa_id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('status', 'aktif')
            ->first();

        $tabunganSiswa = $data['siswaKelas']->siswa->tabungan_siswa ?? collect();

        $tabunganHelper = new \App\Services\TabunganSiswaService();
        $saldoTabungan = $tabunganHelper->tambahSaldoAkhir($tabunganSiswa);
        $countSaldo = $saldoTabungan->count();

        $data['totalSaldo'] = $saldoTabungan ? $saldoTabungan[$countSaldo - 1]->saldo_akhir : 0;

        $data['transaksi'] = $request->transaksi;

        return view('pages.tabungan_siswa.form',$data);
    }

    public function store(Request $request)
    {
        $rules = [
            'siswa_kelas_id' => 'required|exists:mysql_siakad.siswa_kelas,id',
            'nominal' => 'required|numeric',
        ];
        $messages = [
            'siswa_kelas_id.required' => 'Siswa harus dipilih',
            'siswa_kelas_id.exists' => 'Siswa tidak ditemukan',
            'nominal.required' => 'Nominal harus diisi',
        ];
        $attributes = [
            'siswa_kelas_id' => 'Siswa Kelas',
            'nominal' => 'Nominal',
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
            if($request->transaksi == 'setor'){
                //custom tanggal
                $inputDate = date('Y-m-d', strtotime($request->tanggal));
                $currentTime = date('H:i:s');
                $tanggal = $inputDate . ' ' . $currentTime;

                $siswakelas = SiswaKelas::find($request->siswa_kelas_id);
                $siswaid = $siswakelas->siswa_id;

                $tabungan_siswa = TabunganSiswa::create([
                    'siswa_id' => $siswaid,
                    'debit' => $request->nominal,
                    'kredit' => 0,
                    'tanggal' => $tanggal,
                ]);

                Ledger::create([
                    'sumber_tabel' => 'tabungan_siswa',
                    'referensi_id' => $tabungan_siswa->id,
                    'tipe' => 'in',
                    'jenis_akun' => null,
                    'trx_date' => $tanggal,
                    'keterangan' => 'Setor tabungan siswa',
                    'debit' => $request->nominal,
                    'kredit' => 0
                ]);
            }else{
                // Handle withdrawal transaction
                $inputDate = date('Y-m-d', strtotime($request->tanggal));
                $currentTime = date('H:i:s');
                $tanggal = $inputDate . ' ' . $currentTime;

                $siswakelas = SiswaKelas::find($request->siswa_kelas_id);
                $siswaid = $siswakelas->siswa_id;

                $tabungan_siswa = TabunganSiswa::create([
                    'siswa_id' => $siswaid,
                    'debit' => 0,
                    'kredit' => $request->nominal,
                    'tanggal' => $tanggal,
                ]);

                Ledger::create([
                    'sumber_tabel' => 'tabungan_siswa',
                    'referensi_id' => $tabungan_siswa->id,
                    'tipe' => 'out',
                    'jenis_akun' => null,
                    'trx_date' => $tanggal,
                    'keterangan' => 'Penarikan tabungan siswa',
                    'debit' => 0,
                    'kredit' => $request->nominal
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan',
                'data' => $tabungan_siswa
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
}
