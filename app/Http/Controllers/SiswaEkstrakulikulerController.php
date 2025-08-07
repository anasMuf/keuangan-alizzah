<?php

namespace App\Http\Controllers;

use App\Models\Bulan;
use App\Helpers\LogPretty;
use App\Models\PosPemasukan;
use App\Models\SIAKAD\Kelas;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Models\SiswaEkstrakulikuler;
use App\Services\TagihanSiswaService;
use Illuminate\Support\Facades\Validator;

class SiswaEkstrakulikulerController extends Controller
{
    public function index(Request $request)
    {

        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Pos Pemasukan',
            'Keterangan',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

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
            $siswaEkstrakulikulerQuery = SiswaEkstrakulikuler::with([
                'pos_pemasukan',
                'siswa_kelas.siswa',
                'siswa_kelas.kelas',
            ])
            ->where('siswa_kelas_id', $siswaKelasId)
            ->get();


            $btnDelete = '';
            $btnDetails = '';
            $no = 1;

            foreach($siswaEkstrakulikulerQuery as $item){

                $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->pos_pemasukan->nama_pos_pemasukan.'`)">
                    <i class="fa fa-lg fa-fw fa-trash"></i>
                </button>';
                $btnDetails = '<a href="'.route('siswa_ekstrakulikuler.form', ['siswa_id' => $siswaKelasId, 'id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                    <i class="fa fa-lg fa-fw fa-eye"></i>
                </a>';

                $data['config']['data'][] = [
                    $no++,
                    $item->pos_pemasukan->nama_pos_pemasukan ?? '-',
                    $item->keterangan ?? '-',
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

        return view('pages.siswa_ekstrakulikuler.index', $data);
    }

    public function form(Request $request)
    {
        $data['data'] = null;
        $data['tagihanSiswa'] = null;
        if($request->id) {
            $data['data'] = SiswaEkstrakulikuler::with(['siswa_kelas.siswa', 'pos_pemasukan.tagihan_siswa'])
                ->findOrFail($request->id);
            $ke = $data['data']->ke;
            foreach($data['data']->pos_pemasukan->tagihan_siswa as $item){
                if($item->jumlah_harus_dibayar == $ke){
                    $data['tagihanSiswa'] = $item;
                }
            }
        }

        $data['siswaKelas'] = SiswaKelas::with(['siswa', 'kelas.jenjang', 'tahun_ajaran'])
            ->where('status', 'aktif')
            ->whereHas('tahun_ajaran', function($query) {
                $query->where('is_aktif', true);
            })
            ->whereHas('siswa')
            ->where('siswa_id', $request->siswa_id)
            ->first();

        $resultPosPemasukan = [];
        $posPemasukan = PosPemasukan::with([
                'jenjang_pos_pemasukan' => function($q) use ($data) {
                    $q->where('jenjang_id', $data['siswaKelas']->kelas->jenjang_id);
                }
            ])
            ->whereHas('jenjang_pos_pemasukan',function($q) use ($data) {
                    $q->where('jenjang_id', $data['siswaKelas']->kelas->jenjang_id);
                })
            ->where('wajib',true)->where('optional',true)
            ->get()->toArray();
        foreach (
            $posPemasukan
            as $item
        ) {
            $resultPosPemasukan[$item['id']] = $item['nama_pos_pemasukan'];
        }
        $data['pos_pemasukan'] = $resultPosPemasukan;
        $posPemaukanSelected = [];
        if($data['data']){
            $posPemaukanSelected[] = (string)$data['data']->pos_pemasukan_id;
        }
        $data['posPemaukanSelected'] = $posPemaukanSelected;

        return view('pages.siswa_ekstrakulikuler.form', $data);
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

            $ke = 1;

            $keTerakhir = SiswaEkstrakulikuler::where('pos_pemasukan_id',$request->pos_pemasukan_id)->orderBy('ke','desc')->first();
            if($keTerakhir){
                $ke++;
            }

            $siswaEkstrakulikuler = SiswaEkstrakulikuler::create([
                'siswa_kelas_id' => $request->siswa_kelas_id,
                'pos_pemasukan_id' => $request->pos_pemasukan_id,
                'keterangan' => $request->keterangan,
                'ke' => $ke
            ]);
            if(!$siswaEkstrakulikuler) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan Siswa Ekstrakulikuler'
                ]);
            }

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
                'message' => 'Ekstrakulikuler dan Tagihan Siswa saved successfully',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan ekstrakulikuler dan tagihan siswa: ' . $th->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $siswaEkstrakulikuler = SiswaEkstrakulikuler::findOrFail($id);
            TagihanSiswa::where('pos_pemasukan_id', $siswaEkstrakulikuler->pos_pemasukan_id)->delete();
            $siswaEkstrakulikuler->delete();

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
