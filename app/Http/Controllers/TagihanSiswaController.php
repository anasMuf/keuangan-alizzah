<?php

namespace App\Http\Controllers;

use App\Helpers\LogPretty;
use App\Models\SIAKAD\Desa;
use App\Models\PosPemasukan;
use App\Models\SIAKAD\Kelas;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use App\Models\SiswaDispensasi;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Services\TagihanSiswaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TagihanSiswaController extends Controller
{
    public function index(Request $request)
    {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Bulan',
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
            'columns' => [null, null, null, null, null, null, null, ['orderable' => false]]
        ];
        $data['config']['paging'] = false;
        $data['config']["lengthMenu"] = [ 50, 100, 500];
        $data['config']['data'] = [];
        $data['siswa'] = null;
        $data['siswaKelas'] = null;

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

            // Query tagihan siswa berdasarkan siswa_kelas_id
            $tagihanSiswa = TagihanSiswa::with([
                'tahun_ajaran',
                'bulan',
                'pos_pemasukan',
                'siswa_kelas.siswa',
                'siswa_kelas.kelas',
                'siswa_dispensasi.kategori_dispensasi'
            ])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('siswa_kelas_id', $siswaKelasId)
            ->get();


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
            ->get();

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
            $resultTagihan = $tagihanSiswaService->create($dataTagihan, $request->pos_pemasukan_id);
            if(!$resultTagihan['success']) {
                DB::rollBack();
                LogPretty::error('Gagal membuat tagihan siswa, id: ' . $request->siswa_kelas_id . ': ' . $resultTagihan['message']);
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
            LogPretty::error('Gagal menyimpan tagihan siswa: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan tagihan siswa: ' . $th->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        // Logic to delete tagihan siswa
        // This is a placeholder for the actual implementation
        // You can find the tagihan by ID and delete it from the database

        return response()->json(['message' => 'Tagihan Siswa deleted successfully']);
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
                    LogPretty::error('Gagal memperbarui tagihan siswa, id: ' . $siswaKelasId . ': ' . $resultTagihan['message']);
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


            $tagihanSiswa = TagihanSiswa::where('tahun_ajaran_id', $tahunAjaran->id)
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

                $value->update([
                    'siswa_dispensasi_id' => $siswa_dispensasi_id,
                    'diskon_persen' => $diskonPersen,
                    'diskon_nominal' => $diskonNominal,
                    'nominal' => $totalNominal
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
}
