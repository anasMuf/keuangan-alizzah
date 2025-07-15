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
        with('jenjang_pos_pemasukan.jenjang','jenjang_pos_pemasukan.tahun_ajaran')->
        get();

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

            $data['config']['data'][] = [
                $no++,
                // $tahun_ajaran,
                $item->nama_pos_pemasukan,
                $item->pembayaran,
                $jenjang,
                'Rp '.number_format($item->nominal_valid,0,',','.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pos_pemasukan.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? PosPemasukan::with('jenjang_pos_pemasukan.jenjang','jenjang_pos_pemasukan.tahun_ajaran')->find($request->id) : [];

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

    public function settingNominalBulan(Request $request)
    {
        $data['tagihanBulan'] = TagihanSiswa::select('id','bulan_id','nominal')->with('bulan:id,nama_bulan')->where('pos_pemasukan_id', $request->id_pos_pemasukan)->groupBy('bulan_id')->get();
        $result = view('pages.pos_pemasukan.modal', $data)->render();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendapatkan data',
            'result' => $result,
        ]);
    }

    public function storeNominalBulan(Request $request)
    {
        $rules = [
            'id_pos_pemasukan' => 'required|exists:pos_pemasukan,id',
            'bulan_id' => 'required|array',
            'nominal_bulan' => 'required|array',
            'nominal_bulan.*' => 'numeric|min:0',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'exists' => ':attribute tidak ditemukan',
            'numeric' => ':attribute harus berupa angka',
            'min' => ':attribute tidak boleh kurang dari 0',
        ];
        $attributes = [
            'id_pos_pemasukan' => 'Pos Pemasukan',
            'bulan_id' => 'Bulan',
            'nominal_bulan' => 'Nominal Bulan',
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        DB::beginTransaction();
        try {
            foreach ($request->bulan_id as $index => $bulanId) {
                TagihanSiswa::where([
                    ['tahun_ajaran_id', TahunAjaran::where('is_aktif', true)->first()->id],
                    ['bulan_id', $bulanId],
                    ['pos_pemasukan_id', $request->id_pos_pemasukan],
                ])->update([
                    'nominal_awal' => $request->nominal_bulan[$index],
                    'nominal' => $request->nominal_bulan[$index],
                ]);
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
}
