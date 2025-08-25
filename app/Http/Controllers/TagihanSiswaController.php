<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Models\Pemasukan;
use App\Helpers\LogPretty;
use App\Models\SIAKAD\Desa;
use App\Models\PosPemasukan;
use App\Models\SIAKAD\Kelas;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use App\Models\PemasukanDetail;
use App\Models\SiswaDispensasi;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TagihanSiswaService;
use Illuminate\Support\Facades\Validator;

class TagihanSiswaController extends Controller
{
    public function index(Request $request)
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Bulan',
            'Tagihan ke',
            'Pos Pemasukan',
            'Dispensasi',
            'Nilai Dispensasi',
            'Nominal',
            'Status',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, null, null, null, null, ['orderable' => false]]
        ];
        $data['config']['paging'] = false;
        $data['config']["lengthMenu"] = [ 50, 100, 500];
        $data['config']['data'] = [];
        $data['siswa'] = null;
        $data['siswaKelas'] = null;

        $bulanSekarang = Bulan::where('angka_bulan', date('n'))->first();
        if($request->siswa_id) {
            // Ambil tahun ajaran aktif
            $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
            // Ambil daftar siswa_kelas_id dari database siakad
            $siswaKelas = SiswaKelas::where('siswa_id', $request->siswa_id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('status', 'aktif')
                ->firstOrFail();
            $data['siswaKelas'] = $siswaKelas;
            $siswaKelasId = $siswaKelas->id;

            // Query tagihan siswa berdasarkan siswa_kelas_id dan logika bulan_id
            $tagihanSiswaQuery = TagihanSiswa::with([
                'tahun_ajaran',
                'bulan',
                'pos_pemasukan',
                'siswa_kelas.siswa',
                'siswa_kelas.kelas',
                'siswa_dispensasi.kategori_dispensasi'
            ])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('siswa_kelas_id', $siswaKelasId);

            if (!isset($request->bulan_id)) {
                // Tidak ada bulan_id, ambil bulan sekarang saja
                $tagihanSiswaQuery->where(function($q) use ($bulanSekarang){
                    $q->where('bulan_id', $bulanSekarang->id)
                    ->orWhereNull('bulan_id');
                });
            } elseif (isset($request->bulan_id) && $request->bulan_id !== 'all') {
                // Ada bulan_id dan bukan 'all', ambil sesuai request
                $tagihanSiswaQuery->where(function($q) use ($request){
                    $q->where('bulan_id', $request->bulan_id)
                    ->orWhereNull('bulan_id');
                });
            }
            // Jika bulan_id == 'all', tidak perlu filter bulan_id

            $tagihanSiswa = $tagihanSiswaQuery->get();


            $btnDelete = '';
            $btnDetails = '';
            $status = '';
            $dispensasi = '';
            $nilaiDispensasi = '';
            $no = 1;

            foreach($tagihanSiswa as $item){

                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->siswa_kelas->siswa->nama_lengkap.'`)">
                    <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>';
                // $btnDetails = '<a href="'.route('tagihan_siswa.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                //     <i class="fa fa-lg fa-fw fa-eye"></i>
                // </a>';

                if($item->siswa_dispensasi){
                    $dispensasi = $item->siswa_dispensasi->kategori_dispensasi->nama_kategori;
                }else{
                    $dispensasi = '-';
                }

                if($item->diskon_persen > 0){
                    $nilaiDispensasi = ($item->diskon_persen*100).'%';
                }elseif($item->diskon_nominal > 0){
                    $nilaiDispensasi = 'Rp '.number_format($item->diskon_nominal, 0, ',', '.');
                }else{
                    $nilaiDispensasi = '-';
                }

                if($item->status == 'lunas') {
                    $status = '<span class="badge bg-success">Lunas</span>';
                } elseif($item->status == 'belum_lunas') {
                    $status = '<span class="badge bg-warning">Belum Lunas</span>';
                } elseif($item->status == 'belum_bayar') {
                    $status = '<span class="badge bg-danger">Belum Bayar</span>';
                } else {
                    $status = '<span class="badge bg-secondary">Tidak Aktif</span>';
                }

                $data['config']['data'][] = [
                    $no++,
                    $item->bulan->nama_bulan ?? '-',
                    $item->jumlah_harus_dibayar ?? '-',
                    $item->pos_pemasukan->nama_pos_pemasukan ?? '-',
                    $dispensasi,
                    $nilaiDispensasi,
                    'Rp '.number_format($item->nominal, 0, ',', '.'),
                    $status,
                    '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
                ];
            }
        }

        $data['kelas'] = Kelas::get();
        $data['siswa_kelas'] = SiswaKelas::with(['siswa', 'kelas.jenjang', 'tahun_ajaran'])
            ->where('status', 'aktif')
            ->whereHas('tahun_ajaran', function($query) {
                $query->where('is_aktif', true);
            })
            ->whereHas('siswa')
            ->get();
        $data['bulans'] = Bulan::all();
        $data['bulanSekarang'] = $bulanSekarang;

        return view('pages.tagihan_siswa.index', $data);
    }

    public function form(Request $request)
    {
        if(!$request->siswa_id) {
            return redirect()->back()->with([
                'message' => 'Siswa tidak ditemukan.',
                'success' => false,
            ]);
        }

        $data['data'] = ($request->id) ? TagihanSiswa::with([
            'tahun_ajaran',
            'bulan',
            'pos_pemasukan',
            'siswa_kelas.siswa',
            'siswa_kelas.kelas',
            'siswa_dispensasi',
            'pemasukan_detail.pemasukan_pembayaran',
            'pemasukan_detail.pemasukan'
        ])->findOrFail($request->id) : null;

        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $siswaKelas = SiswaKelas::where('siswa_id', $request->siswa_id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('status', 'aktif')
            ->firstOrFail();
        $data['siswaKelas'] = $siswaKelas;

        $resultPosPemasukan = [];
        foreach (PosPemasukan::get()->toArray() as $item) {
            $resultPosPemasukan[$item['id']] = $item['nama_pos_pemasukan'];
        }
        $data['pos_pemasukan'] = $resultPosPemasukan;
        $posPemaukanSelected = [];
        if($data['data']){
            $posPemaukanSelected[] = (string)$data['data']->pos_pemasukan_id;
        }
        $data['posPemaukanSelected'] = $posPemaukanSelected;

        return view('pages.tagihan_siswa.form', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'siswa_kelas_id' => 'required',
            'pos_pemasukan_id' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'siswa_kelas_id' => 'Siswa Kelas',
            'pos_pemasukan_id' => 'Pos Pemasukan',
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

            $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
            $tagihanSiswaService = new TagihanSiswaService();
            $dataTagihan = [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'siswa_kelas' => SiswaKelas::with('kelas.jenjang','siswa.siswa_dispensasi')->find($request->siswa_kelas_id)->toArray(),
                'siswa_dispensasi' => $siswaKelas['siswa']['siswa_dispensasi'] ?? [],
            ];
            // return $dataTagihan;
            $resultTagihan = $tagihanSiswaService->create($dataTagihan, $request->pos_pemasukan_id,$request->nominal);
            if(!$resultTagihan['success']) {
                DB::rollBack();
                LogPretty::info('Gagal membuat tagihan siswa, id: ' . $request->siswa_kelas_id . ': ' . $resultTagihan['message']);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat tagihan siswa: ' . $resultTagihan['message']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tagihan Siswa saved successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan tagihan siswa: ' . $th->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $tagihanSiswa = TagihanSiswa::with(
                'pemasukan_detail:id,tagihan_siswa_id,pemasukan_id',
                'pemasukan_detail.pemasukan:id'
            )
            ->where('id', $id)->firstOrFail();
            $pemasukanDetail = PemasukanDetail::where('tagihan_siswa_id', $tagihanSiswa->id)->first();
            // update total pemasukan jika pemasukanDetail ada
            if ($pemasukanDetail) {
                $pemasukan = Pemasukan::findOrFail($tagihanSiswa->pemasukan_detail->pemasukan->id);
                $totalSekarang = $pemasukan->total;
                $totalBaru = $totalSekarang - $tagihanSiswa->nominal;
                if ($totalBaru < 0) {
                    DB::rollBack();
                    LogPretty::info('pemasukan:',$pemasukan);
                    LogPretty::info('total sekarang:',$totalSekarang);
                    LogPretty::info('total baru:',$totalBaru);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menghapus data, total pemasukan tidak boleh kurang dari 0',
                    ]);
                }
                $pemasukan->total -= $tagihanSiswa->nominal;
                $pemasukan->save();
                $pemasukanDetail->delete();
            }
            $tagihanSiswa->delete();

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

    public function generateTagihanSiswa()
    {
        DB::beginTransaction();
        try {
            $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
            if (!$tahunAjaran) {
                return response()->json(['message' => 'Tahun Ajaran aktif tidak ditemukan'], 404);
            }

            $siswaKelas = SiswaKelas::where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('status', 'aktif')
                ->pluck('id');
            if ($siswaKelas->isEmpty()) {
                return response()->json(['message' => 'Tidak ada siswa kelas aktif untuk tahun ajaran ini'], 404);
            }

            $dataTagihan = [];
            foreach($siswaKelas as $siswaKelasId) {
                $tagihanSiswaService = new TagihanSiswaService();
                $dataTagihan = [
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'siswa_kelas' => SiswaKelas::with('kelas.jenjang','siswa.siswa_dispensasi')->find($siswaKelasId)->toArray(),
                    'siswa_dispensasi' => $siswaKelas['siswa']['siswa_dispensasi'] ?? [],
                ];
                // return $dataTagihan;
                $resultTagihan = $tagihanSiswaService->create($dataTagihan);
                // LogPretty::info('Hasil memperbarui tagihan siswa: ' . json_encode($resultTagihan, JSON_PRETTY_PRINT));
                if(!$resultTagihan['success']) {
                    LogPretty::info('Gagal memperbarui tagihan siswa, id: ' . $siswaKelasId . ': ' . $resultTagihan['message']);
                }
            }

            DB::commit();
            return redirect()->back()->with([
                'message' => 'Tagihan siswa berhasil diperbarui.',
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return redirect()->back()->with([
                'message' => 'Gagal memperbarui tagihan siswa: ' . $th->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function updateDispensasi(Request $request){
        $rules = [
            'siswa_id' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'siswa_id' => 'Siswa',
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
            $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
            if (!$tahunAjaran) {
                DB::rollBack();
                return response()->json(['message' => 'Tahun Ajaran aktif tidak ditemukan']);
            }

            $siswaKelas = SiswaKelas::
                where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('siswa_id', $request->siswa_id)
                ->where('status', 'aktif')
                ->first();
            if (!$siswaKelas) {
                DB::rollBack();
                return response()->json(['message' => 'Tidak ada siswa kelas aktif untuk tahun ajaran ini']);
            }

            $siswaDispensasi = SiswaDispensasi::where('siswa_id',$siswaKelas->siswa_id)->get();
            $siswaDispensasiPosPemasukan = $siswaDispensasi->pluck('pos_pemasukan_id');


            $tagihanSiswa = TagihanSiswa::with('pemasukan_detail')->where('tahun_ajaran_id', $tahunAjaran->id)
                ->where('siswa_kelas_id', $siswaKelas->id)
                ->whereIn('pos_pemasukan_id', $siswaDispensasiPosPemasukan)
                ->get();
            if ($tagihanSiswa->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tagihan siswa untuk siswa ini'
                ]);
            }

            foreach($tagihanSiswa as $value){
                $diskonPersen = $value->diskon_persen;
                $diskonNominal = $value->diskon_nominal;
                $totalNominal = 0;
                $siswa_dispensasi_id = null;

                foreach($siswaDispensasi as $item){
                    if($value->pos_pemasukan_id == $item->pos_pemasukan_id){
                        $siswa_dispensasi_id = $item->id;
                        if($item->persentase_overide > 0) {
                            $diskonPersen = $item->persentase_overide;
                            $diskonNominal = 0;
                            Log::info("Menggunakan persentase overide untuk pos pemasukan {$item->pos_pemasukan_id}: {$diskonPersen}");
                        } elseif($item->nominal_overide > 0) {
                            $diskonNominal = $item->nominal_overide;
                            $diskonPersen = 0;
                            Log::info("Menggunakan nominal overide untuk pos pemasukan {$item->pos_pemasukan_id}: {$diskonNominal}");
                        }else{
                            Log::info(json_encode([$item->persentase_overide,$item->nominal_overide],JSON_PRETTY_PRINT));
                        }
                        break;
                    }
                }
                if($diskonPersen > 0){
                    $totalNominal = $value->nominal_awal - ($value->nominal_awal * $diskonPersen);
                    Log::info("Menghitung total nominal untuk tagihan siswa {$value->id} dengan diskon persen: {$diskonPersen}%, total nominal: {$totalNominal}");
                }elseif($diskonNominal > 0){
                    $totalNominal = $value->nominal_awal - $diskonNominal;
                    Log::info("Menghitung total nominal untuk tagihan siswa {$value->id} dengan diskon nominal: {$diskonNominal}, total nominal: {$totalNominal}");
                }else{
                    $totalNominal = $value->nominal_awal;
                    Log::info($totalNominal);
                }

                $dibayar = 0;
                $status = $value->status;
                foreach($value->pemasukan_detail as $detail){
                    $dibayar += $detail->subtotal;
                }
                if($totalNominal >= $dibayar){
                    $status = 'lunas';
                }

                $value->update([
                    'siswa_dispensasi_id' => $siswa_dispensasi_id,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'nominal' => $totalNominal,
                    'status' => $status
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Berhasil update dispensasi di tagihan siswa"
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui dispensasi tagihan siswa: ' . $th->getMessage()
            ]);
        }
    }
    public function getTagihanSiswaByPosPemasukan(Request $request, $id)
    {
        try{
            // First, get TagihanSiswa data
            $query = TagihanSiswa::with([
                'bulan',
                'pos_pemasukan'
            ])
            ->select([
                'id',
                'bulan_id',
                'siswa_kelas_id',
                'pos_pemasukan_id',
                'nominal',
                'diskon_persen',
                'diskon_nominal',
                'nominal_awal',
                'jumlah_harus_dibayar',
                'status',
            ])
            ->where('pos_pemasukan_id', $id);

            $tagihanSiswa = $query->get();

            if ($tagihanSiswa->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Get unique siswa_kelas_id from tagihan
            $siswaKelasIds = $tagihanSiswa->pluck('siswa_kelas_id')->unique()->values();

            // Get siswa_kelas data from SIAKAD database
            $siswaKelasQuery = \App\Models\SIAKAD\SiswaKelas::with([
                'siswa:id,nama_lengkap,jenis_kelamin',
                'kelas.jenjang:id,nama_jenjang'
            ])
            ->whereIn('id', $siswaKelasIds);

            // Apply filters if provided
            if ($request->kelas_id) {
                $siswaKelasQuery->where('kelas_id', $request->kelas_id);
            }

            if ($request->jenjang_id) {
                $siswaKelasQuery->whereHas('kelas', function($q) use ($request) {
                    $q->where('jenjang_id', $request->jenjang_id);
                });
            }

            $siswaKelasData = $siswaKelasQuery->get()->keyBy('id');

            // Filter tagihan based on siswa_kelas that meet the filter criteria
            $filteredTagihan = $tagihanSiswa->filter(function($tagihan) use ($siswaKelasData) {
                return $siswaKelasData->has($tagihan->siswa_kelas_id);
            });

            // Combine the data
            $result = $filteredTagihan->map(function($tagihan) use ($siswaKelasData) {
                $siswaKelas = $siswaKelasData->get($tagihan->siswa_kelas_id);

                return [
                    'id' => $tagihan->id,
                    'bulan_id' => $tagihan->bulan_id,
                    'siswa_kelas_id' => $tagihan->siswa_kelas_id,
                    'pos_pemasukan_id' => $tagihan->pos_pemasukan_id,
                    'nominal' => $tagihan->nominal,
                    'diskon_persen' => $tagihan->diskon_persen,
                    'diskon_nominal' => $tagihan->diskon_nominal,
                    'nominal_awal' => $tagihan->nominal_awal,
                    'jumlah_harus_dibayar' => $tagihan->jumlah_harus_dibayar,
                    'status' => $tagihan->status,
                    'bulan' => $tagihan->bulan,
                    'pos_pemasukan' => $tagihan->pos_pemasukan,
                    'siswa_kelas' => [
                        'id' => $siswaKelas->id ?? null,
                        'siswa' => [
                            'id' => $siswaKelas->siswa->id ?? null,
                            'nama_lengkap' => $siswaKelas->siswa->nama_lengkap ?? 'Unknown',
                            'jenis_kelamin' => $siswaKelas->siswa->jenis_kelamin ?? null,
                        ],
                        'kelas' => [
                            'id' => $siswaKelas->kelas->id ?? null,
                            'nama_kelas' => $siswaKelas->kelas->nama_kelas ?? 'Unknown',
                            'jenjang' => [
                                'id' => $siswaKelas->kelas->jenjang->id ?? null,
                                'nama_jenjang' => $siswaKelas->kelas->jenjang->nama_jenjang ?? 'Unknown',
                            ]
                        ]
                    ]
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getTagihanSiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tagihan siswa'
            ], 500);
        }
    }

    public function updateNominal(Request $request, $id)
    {
        $rules = [
            'nominal' => 'required|numeric|min:0',
        ];

        $messages = [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus berupa angka',
            'min' => ':attribute tidak boleh kurang dari 0',
        ];

        $attributes = [
            'nominal' => 'Nominal',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            $tagihan = TagihanSiswa::findOrFail($id);

            $tagihan->update([
                'nominal' => $request->nominal,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengupdate nominal tagihan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate nominal tagihan',
            ]);
        }
    }
}
