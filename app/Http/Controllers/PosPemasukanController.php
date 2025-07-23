<?php

namespace App\Http\Controllers;

use App\Models\Pos;
use App\Models\Bulan;
use App\Helpers\LogPretty;
use App\Models\PosPemasukan;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use App\Models\SIAKAD\Jenjang;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Support\Facades\DB;
use App\Models\JenjangPosPemasukan;
use Illuminate\Support\Facades\Log;
use App\Models\JenjangPosPemasukanDetail;
use App\Models\JenjangPosPemasukanNominal;
use Illuminate\Support\Facades\Validator;

class PosPemasukanController extends Controller
{
    public function index() {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            // 'Tahun Ajaran',
            'Nama Pos Pemasukan',
            'Tipe Pembayaran',
            'Jenjang',
            'Nominal',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pos_pemasukan =  PosPemasukan::
        with(
            'jenjang_pos_pemasukan.jenjang',
            'jenjang_pos_pemasukan.tahun_ajaran',
            'jenjang_pos_pemasukan.jenjang_pos_pemasukan_detail.jenjang_pos_pemasukan_nominal'
        )->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                // null,
                null,
                null,
                null,
                null,
                null,
                ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $jenjang = '';
        $tahun_ajaran = '';
        $nominal = '';
        $no = 1;

        foreach($pos_pemasukan as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_pos_pemasukan.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('pos_pemasukan.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $jenjang = '<ul>';
            foreach ($item->jenjang_pos_pemasukan as $value) {
                $tahun_ajaran = $value->tahun_ajaran->nama_tahun_ajaran;
                $jenjang .= '<li>'.$value->jenjang->nama_jenjang.'</li>';
            }
            $jenjang .= '</ul>';

            $nominal = 'Rp '.number_format($item->nominal_valid,0,',','.');
            if($item->is_nominal_varian){
                $nominal = "lihat detail";
            }

            if($item->tabungan && !$item->wajib){
                $nominal = 'nominal menyesuaikan';
            }

            $data['config']['data'][] = [
                $no++,
                // $tahun_ajaran,
                $item->nama_pos_pemasukan,
                $item->pembayaran,
                $jenjang,
                $nominal,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pos_pemasukan.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? PosPemasukan::with(
            'jenjang_pos_pemasukan.jenjang',
            'jenjang_pos_pemasukan.tahun_ajaran',
            'jenjang_pos_pemasukan.jenjang_pos_pemasukan_detail.jenjang_pos_pemasukan_nominal.bulan'
        )->find($request->id) : [];

        $resultPos = [];
        foreach (Pos::get()->toArray() as $item) {
            $resultPos[$item['id']] = $item['nama_pos'];
        }
        $data['pos'] = $resultPos;

        $data['jenis'] = ['Insidential','Wajib'];
        $jenisSelected = '';
        if($data['data']){
            $jenisSelected = $data['data']->wajib ? 1 : 0;
        }
        $data['jenisSelected'] = $jenisSelected;

        $data['pembayaran'] = ['sekali'=>'Sekali', 'harian'=>'Harian', 'mingguan'=>'Mingguan', 'bulanan'=>'Bulanan', 'tahunan'=>'Tahunan'];
        $pembayaranSelected = '';
        if($data['data']){
            $pembayaranSelected = $data['data']->pembayaran;
        }
        $data['pembayaranSelected'] = $pembayaranSelected;

        $resultJenjang = [];
        foreach (Jenjang::get()->toArray() as $item) {
            $resultJenjang[$item['id']] = $item['nama_jenjang'];
        }
        $data['jenjang'] = $resultJenjang;
        $jenjangSelected = [];
        if($data['data']){
            foreach ($data['data']->jenjang_pos_pemasukan as $value) {
                $jenjangSelected[] = (string)$value->jenjang_id;
            }
        }
        $data['jenjangSelected'] = $jenjangSelected;

        $resultTahunAjaran = [];
        foreach (TahunAjaran::get()->toArray() as $item) {
            $resultTahunAjaran[$item['id']] = $item['nama_tahun_ajaran'];
        }
        $data['tahun_ajaran'] = $resultTahunAjaran;
        $tahunAjaranSelected = [];
        if($data['data']){
            foreach ($data['data']->jenjang_pos_pemasukan as $value) {
                $tahunAjaranSelected[0] = (string)$value->tahun_ajaran_id;
            }
        }
        $data['tahunAjaranSelected'] = $tahunAjaranSelected;

        $data['config'] = [
            "placeholder" => ".:: Pilih Beberapa Jenjang ::.",
            "theme" => 'bootstrap4',
            "width" => '100%'
        ];

        // return $data['data'];
        return view('pages.pos_pemasukan.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'jenis' => 'required',
            'pembayaran' => 'required',
            'nama_pos_pemasukan' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus berupa angka',
        ];
        $attributes = [
            'jenis' => 'Jenis Pemasukan',
            'pembayaran' => 'Pembayaran',
            'nama_pos_pemasukan' => 'Nama Pos Pemasukan',
            'nominal_valid' => 'Nominal',
        ];

        // if((boolean)$request->jenis){
            $rules['nominal_valid'] = 'required|numeric';
        // }else{
        //     $request->merge([
        //         'nominal_valid' => 0,
        //     ]);
        // }

        $request->merge([
            'pos_id' => 1,
            'wajib' => (int)$request->jenis,
        ]);
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
            // Simpan data pos pemasukan terlebih dahulu
            $pemasukan = PosPemasukan::updateOrCreate(['id'=>$request->id_pos_pemasukan],$request->all());

            // Simpan ID jenjang yang ada dalam request untuk pengecekan nanti
            $jenjangIdsInRequest = $request->jenjang_id ?? [];

            // Hapus relasi jenjang yang tidak ada dalam request (jika edit)
            if ($request->id_pos_pemasukan) {
                JenjangPosPemasukan::where('pos_pemasukan_id', $pemasukan->id)
                    ->whereNotIn('jenjang_id', $jenjangIdsInRequest)
                    ->delete();
            }

            // Proses setiap jenjang dalam request
            foreach ($jenjangIdsInRequest as $jenjangId) {
                // Cari jika relasi sudah ada, update atau buat baru jika belum ada
                $jenjangPosPemasukan = JenjangPosPemasukan::updateOrCreate(
                    [
                        'pos_pemasukan_id' => $pemasukan->id,
                        'jenjang_id' => $jenjangId
                    ],
                    [
                        'tahun_ajaran_id' => $request->tahun_ajaran_id
                    ]
                );

                if (!$jenjangPosPemasukan) {
                    DB::rollBack();
                    Log::error('Gagal menyimpan data Pos Pemasukan untuk Jenjang ID: ' . $jenjangId);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan data Pos Pemasukan untuk Jenjang',
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, kesalahan pada sistem',
            ]);
        }
    }

    public function delete($id){
        DB::beginTransaction();
        try {
            $pos_pemasukan = PosPemasukan::findOrFail($id);
            JenjangPosPemasukan::where('pos_pemasukan_id',$pos_pemasukan->id)->delete();
            $pos_pemasukan->delete();

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

    public function formJenjangPosPemasukanDetail(Request $request){
        $data['data'] = [];

        $data['jenisKelamin'] = ['Laki-laki'=>'Laki-laki','Perempuan'=>'Perempuan'];
        $jenisKelaminSelected = '';
        if($data['data']){
            $jenisKelaminSelected = $data['data']->jenis_kelamin;
        }
        $data['jenisKelaminSelected'] = $jenisKelaminSelected;

        $data['pos_pemasukan'] = PosPemasukan::with([
            'jenjang_pos_pemasukan' => function($query) use ($request){
                $query->where('id',$request->jenjang_pos_pemasukan_id);
            },
            'jenjang_pos_pemasukan.jenjang'
        ])
        ->findOrFail($request->pos_pemasukan_id);
        $data['bulan'] = Bulan::get();

        $result = view('pages.pos_pemasukan.modal_jenjang_pos_pemasukan_detail',$data)->render();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'result' => $result,
        ]);
    }

    public function storeJenjangPosPemasukanDetail(Request $request){
        $rules = [
            'id_pos_pemasukan' => 'required',
            'is_nominal_bulanan' => 'required',
            'jenis_kelamin' => 'nullable',
            'jenjang_pos_pemasukan_id' => 'required',
            'nominal_valid' => 'nullable|numeric|min:0',
            'bulan' => 'nullable|array',
            'bulan.*.id' => 'nullable',
            'bulan.*.nominal' => 'nullable|numeric|min:0',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'exists' => ':attribute tidak ditemukan',
        ];
        $attributes = [
            'id_pos_pemasukan' => 'Pos Pemasukan',
            'is_nominal_bulanan' => 'Nominal Bulanan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'jenjang_pos_pemasukan_id' => 'Jenjang Pos Pemasukan',
            'nominal_valid' => 'Nominal Valid',
            'bulan' => 'Bulan',
            'bulan.*.id' => 'ID Bulan',
            'bulan.*.nominal' => 'Nominal Bulan',
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

            // simpan jenjang pos pemasukan detail
            $jenjangPosPemasukanDetail = JenjangPosPemasukanDetail::create(
                [
                    'jenjang_pos_pemasukan_id' => $request->jenjang_pos_pemasukan_id,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'nominal_valid' => $request->nominal_valid??0,
                    'is_nominal_bulan' => $request->is_nominal_bulanan == 'bulanan' ? true : false
                ]
            );

            if($request->is_nominal_bulanan == 'bulanan'){
                // simpan nominal bulanan
                foreach ($request->bulan as $item) {
                    JenjangPosPemasukanNominal::create([
                        'jenjang_pos_pemasukan_detail_id' => $jenjangPosPemasukanDetail->id,
                        'bulan_id' => $item['id'],
                        'nominal' => $item['nominal'],
                    ]);
                }
            }




            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, kesalahan pada sistem',
            ]);
        }
    }

    public function formJenjangPosPemasukanNominal(Request $request){
        $data['data'] = JenjangPosPemasukanNominal::find($request->jenjang_pos_pemasukan_nominal_id);
        $result = view('pages.pos_pemasukan.modal_jenjang_pos_pemasukan_nominal',$data)->render();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'result' => $result,
        ]);
    }

    public function updateJenjangPosPemasukanNominal(Request $request){
        $rules = [
            'jenjang_pos_pemasukan_nominal_id' => 'required|exists:jenjang_pos_pemasukan_nominal,id',
            'nominal' => 'required|numeric|min:0',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'exists' => ':attribute tidak ditemukan',
        ];
        $attributes = [
            'jenjang_pos_pemasukan_nominal_id' => 'ID',
            'nominal' => 'Nominal',
        ];

        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            $jenjangPosPemasukanNominal = JenjangPosPemasukanNominal::find($request->jenjang_pos_pemasukan_nominal_id);
            if(!$jenjangPosPemasukanNominal){
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui data, data tidak ditemukan',
                ]);
            }

            $jenjangPosPemasukanNominal->update([
                'nominal' => $request->nominal,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data, kesalahan pada sistem',
            ]);
        }
    }

    public function syncPosPemasukanTagihanSiswa(Request $request){
        $rules = [
            'pos_pemasukan_id' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'pos_pemasukan_id' => 'ID',
        ];

        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            $tahunAjaran = TahunAjaran::where('is_aktif',true)->first();
            if(!$tahunAjaran){
                return response()->json([
                    'success' => false,
                    'message' => 'tahun ajaran tidak tersedia',
                ]);
            }
            //cek tagihan siswa sesuai pos_pemasukan_id
            $tagihanSiswa = TagihanSiswa::select('id','tahun_ajaran_id','bulan_id','siswa_kelas_id','pos_pemasukan_id','nominal_awal','siswa_dispensasi_id','diskon_persen','diskon_nominal','nominal')
            ->with([
                'siswa_kelas.siswa:id,jenis_kelamin',
                'siswa_kelas.kelas:id,nama_kelas,jenjang_id',
                'siswa_kelas.kelas.jenjang:id,nama_jenjang',
                'pos_pemasukan.jenjang_pos_pemasukan.jenjang_pos_pemasukan_detail.jenjang_pos_pemasukan_nominal',
            ])
            ->where('pos_pemasukan_id', $request->pos_pemasukan_id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();
            if(!$tagihanSiswa){
                return response()->json([
                    'success' => true,
                    'message' => 'tidak ada tagihan, tidak ada yang perlu diupdate',
                ]);
            }

            foreach($tagihanSiswa as $value){
                $jenisKelaminSiswa = $value->siswa_kelas->siswa->jenisKelamin;
                $jenjangIdSiswa = $value->siswa_kelas->kelas->jenjang->id;
                $nominalPosPemaukanUntukTagihanSiswa = 0;
                $totalNominal = 0;

                foreach($value->pos_pemasukan->jenjang_pos_pemasukan as $jenjang_pos_pemasukan){
                    if($jenjang_pos_pemasukan->jenjang_id == $jenjangIdSiswa){
                        if($jenjang_pos_pemasukan->jenjang_pos_pemasukan_detail){
                            foreach($jenjang_pos_pemasukan->jenjang_pos_pemasukan_detail as $jenjang_pos_pemasukan_detail){
                                if($jenjang_pos_pemasukan_detail->jenis_kelamin && $jenjang_pos_pemasukan_detail->jenis_kelamin == $jenisKelaminSiswa){
                                    $nominalPosPemaukanUntukTagihanSiswa = $jenjang_pos_pemasukan_detail->nominal;
                                }
                                if($jenjang_pos_pemasukan_detail->is_nominal_bulan){
                                    foreach($jenjang_pos_pemasukan_detail->jenjang_pos_pemasukan_nominal as $jenjang_pos_pemasukan_nominal){
                                        if($jenjang_pos_pemasukan_nominal->bulan_id == $value->bulan_id){
                                            $nominalPosPemaukanUntukTagihanSiswa = $jenjang_pos_pemasukan_nominal->nominal;
                                        }
                                    }
                                }else{
                                    $nominalPosPemaukanUntukTagihanSiswa = $jenjang_pos_pemasukan_detail->nominal;
                                }

                            }
                        }
                    }
                }
                $diskon_persen = $value->diskon_persen ?? 0;
                $diskon_nominal = $value->diskon_nominal ?? 0;
                if($diskon_persen > 0){
                    $totalNominal = $nominalPosPemaukanUntukTagihanSiswa - ($nominalPosPemaukanUntukTagihanSiswa * $diskon_persen);
                }elseif($diskon_nominal > 0){
                    $totalNominal = $nominalPosPemaukanUntukTagihanSiswa - $diskon_nominal;
                }else{
                    $totalNominal = $nominalPosPemaukanUntukTagihanSiswa;
                }

                $value->update([
                    'nominal_awal' => $nominalPosPemaukanUntukTagihanSiswa,
                    'nominal' => $totalNominal
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil sinkron data dengan tagihan siswa',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data, kesalahan pada sistem',
            ]);
        }
    }
}
