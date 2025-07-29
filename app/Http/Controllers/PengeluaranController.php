<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Models\Ledger;
use App\Helpers\LogPretty;
use App\Models\Pengeluaran;
use App\Models\PosPemasukan;
use Illuminate\Http\Request;
use App\Models\PosPengeluaran;
use App\Models\PengeluaranDetail;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Models\PengeluaranPembayaran;
use Illuminate\Support\Facades\Validator;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Tahun Ajaran',
            'No Transaksi',
            'Tanggal',
            'Total',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $bulanSekarang = Bulan::where('angka_bulan', date('n'))->first();

        $pengeluaran = Pengeluaran::with([
            'tahun_ajaran',
        ])
        ->when($request->angka_bulan, function($query) use ($request) {
            $query->whereMonth('tanggal', $request->angka_bulan);
        }, function($query) {
            $query->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'));
        })
        ->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [ null, null, null, null, null, ['orderable' => false]]
        ];

        $no = 1;

        foreach($pengeluaran as $item) {
            $siswa = $item->siswa_kelas->siswa->nama_lengkap ?? '-';
            $kelas = $item->siswa_kelas->kelas->nama_kelas ?? '-';
            $tahun_ajaran = $item->tahun_ajaran->nama_tahun_ajaran ?? '-';

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->no_transaksi.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('pengeluaran.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $tahun_ajaran,
                $item->no_transaksi ?? '-',
                $item->tanggal ? date('d-m-Y', strtotime($item->tanggal)) : '-',
                'Rp '.number_format($item->total ?? 0, 0, ',', '.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }
        $data['bulans'] = Bulan::all();

        return view('pages.pengeluaran.index', $data);
    }

    public function form(Request $request)
    {
        $data['pos'] = PosPemasukan::with('pos_pengeluaran')->has('pos_pengeluaran')->get();
        return view('pages.pengeluaran.form', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'items' => 'required',
            'items.*.pos_pengeluaran_id' => 'required|exists:pos_pengeluaran,id',
            'items.*.keterangan' => 'nullable|string|max:255',
            'items.*.nominal' => 'required|numeric|min:0'
        ];
        $messages = [
            'items.required' => 'Data transaksi tidak boleh kosong.',
            'items.*.pos_pengeluaran_id.required' => 'Pos pengeluaran harus dipilih.',
            'items.*.pos_pengeluaran_id.exists' => 'Pos pengeluaran tidak ditemukan.',
        ];
        $attributes = [
            'items.*.pos_pengeluaran_id' => 'Pos Pengeluaran',
            'items.*.keterangan' => 'Keterangan',
            'items.*.nominal' => 'Nominal'
        ];

        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        $items = json_decode($request->items, true);

        if (!is_array($items) || count($items) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data pengeluaran yang dikirim'
            ]);
        }

        DB::beginTransaction();
        try {
            // 1. Create Pengeluaran (header)
            $pengeluaran = Pengeluaran::create([
                'no_transaksi' => Pengeluaran::generateNoTransaksi(), // Buat method ini jika belum ada
                'tahun_ajaran_id' => TahunAjaran::where('is_aktif', true)->first()->id,
                'tanggal' => now(),
                'total' => 0
            ]);

            $totalPengeluaran = 0;

            foreach ($items as $item) {
                // 2. Create PengeluaranDetail
                $detail = PengeluaranDetail::create([
                    'pengeluaran_id' => $pengeluaran->id,
                    'pos_pengeluaran_id' => $item['pos_pengeluaran_id'],
                    'keterangan' => $item['keterangan'],
                    'nominal' => $item['nominal']
                ]);

                // 3. Create PengeluaranPembayaran
                PengeluaranPembayaran::create([
                    'pengeluaran_id' => $pengeluaran->id,
                    'pengeluaran_detail_id' => $detail->id,
                    'tanggal' => now(),
                    'nominal' => $item['nominal'],
                    'metode' => 'tunai'
                ]);

                // 4. Create Ledger
                Ledger::create([
                    'sumber_tabel' => 'pengeluaran_detail',
                    'referensi_id' => $detail->id,
                    'tipe' => 'out',
                    'jenis_akun' => 'beban',
                    'trx_date' => now(),
                    'keterangan' => 'Pengeluaran',
                    'debit' => 0,
                    'kredit' => $item['nominal']
                ]);

                $totalPengeluaran += $item['nominal'];
            }

            // Update total pengeluaran
            $pengeluaran->update(['total' => $totalPengeluaran]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil disimpan'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pengeluaran: ' . $th->getMessage()
            ]);
        }
    }
}
