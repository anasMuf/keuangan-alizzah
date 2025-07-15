<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Siswa;
use App\Helpers\LogPretty;
use App\Models\PosPemasukan;
use Illuminate\Http\Request;
use App\Models\SiswaDispensasi;
use App\Models\KategoriDispensasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SiswaDispensasiController extends Controller
{
    public function index() {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Siswa ',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $siswa_dispensasi =  SiswaDispensasi::with('siswa','kategori_dispensasi')->groupBy('siswa_id')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($siswa_dispensasi as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->siswa->nama_lengkap.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('siswa_dispensasi.detail', ['siswa_id' => $item->siswa_id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->siswa->nama_lengkap,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.siswa_dispensasi.index', $data);
    }

    public function detail(Request $request) {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Pos Pemasukan ',
            'Nama Dispensasi ',
            'Nilai Dispensasi',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $siswa_dispensasi =  SiswaDispensasi::with('kategori_dispensasi','pos_pemasukan')->where('siswa_id',$request->siswa_id)->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;
        $nilai_dispensasi = '';

        foreach($siswa_dispensasi as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->kategori_dispensasi->nama_kategori.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';

            if(isset($request->from) && $request->from == "siswa"){
                $btnDetails = '<a href="'.route('siswa_dispensasi.form', ['siswa_id' => $item->siswa_id, 'id' => $item->id,'from' => 'siswa']).'" class="btn btn-info btn-xs mx-1" title="Details">
                    <i class="fa fa-lg fa-fw fa-eye"></i>
                </a>';
            }else{
                $btnDetails = '<a href="'.route('siswa_dispensasi.form', ['siswa_id' => $item->siswa_id, 'id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                    <i class="fa fa-lg fa-fw fa-eye"></i>
                </a>';
            }

            if($item->persentase_overide > 0){
                $nilai_dispensasi = $item->persentase_overide*100 .'%';
            }else if($item->nominal_overide > 0){
                $nilai_dispensasi = 'Rp '.number_format($item->nominal_overide,0,',','.');
            }else{
                $nilai_dispensasi = '-';
            }

            $data['config']['data'][] = [
                $no++,
                $item->pos_pemasukan->nama_pos_pemasukan,
                $item->kategori_dispensasi->nama_kategori,
                $nilai_dispensasi,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        $data['siswa'] = Siswa::find($request->siswa_id);
        $data['fromSiswa'] = isset($request->from) && $request->from == "siswa" ? true : false;

        return view('pages.siswa_dispensasi.detail', $data);
    }

    public function form(Request $request){
        if(!$request->id){
            return redirect()->route('siswa.main')->with('warning', 'Jika ingin menambahkan siswa dispensasi, tambahkan siswa atau pilih siswa terlebih dahulu disini');
        }
        $data['data'] = ($request->id) ? SiswaDispensasi::find($request->id) : [];

        $resultKategoriDispensasi = [];
        foreach (KategoriDispensasi::get()->toArray() as $item) {
            $resultKategoriDispensasi[$item['id']] = $item['nama_kategori'];
        }
        $data['kategori_dispensasi'] = $resultKategoriDispensasi;
        $kategoriDispensasiSelected = [];
        if($data['data']){
            $kategoriDispensasiSelected = $data['data']->kategori_dispensasi_id;
        }
        $data['kategoriDispensasiSelected'] = $kategoriDispensasiSelected;

        $resultPosPemasukan = [];
        foreach (PosPemasukan::get()->toArray() as $item) {
            $resultPosPemasukan[$item['id']] = $item['nama_pos_pemasukan'];
        }
        $data['pos_pemasukan'] = $resultPosPemasukan;
        $posPemasukanSelected = [];
        if($data['data']){
            $posPemasukanSelected = $data['data']->pos_pemasukan_id;
        }
        $data['posPemasukanSelected'] = $posPemasukanSelected;


        $data['siswa'] = ($request->siswa_id) ? Siswa::find($request->siswa_id) : null;
        $data['fromSiswa'] = isset($request->from) && $request->from == "siswa" ? true : false;
        return view('pages.siswa_dispensasi.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'siswa_id' => 'required',
            'kategori_dispensasi_id' => 'required',
            'pos_pemasukan_id' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'date' => ':attribute harus berupa tanggal yang valid',
            'after_or_equal' => ':attribute harus setelah atau sama dengan tanggal mulai',
        ];
        $attributes = [
            'siswa_id' => 'Siswa',
            'kategori_dispensasi_id' => 'Kategori Dispensasi',
            'pos_pemasukan_id' => 'Pos Pemasukan',
            'tanggal_mulai' => 'Tanggal Mulai',
            'tanggal_selesai' => 'Tanggal Selesai',
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
        try{

            // Check if the category already exists for the student
            $checkKategoriDispensasiDariSiswa = SiswaDispensasi::where('siswa_id', $request->siswa_id)
                ->where('kategori_dispensasi_id', $request->kategori_dispensasi_id)
                ->first();
            if($checkKategoriDispensasiDariSiswa && $checkKategoriDispensasiDariSiswa->id != $request->id_siswa_dispensasi){
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data, Kategori dispensasi sudah ada untuk siswa ini',
                ]);
            }

            $request->merge([
                'persentase_overide' => $request->persentase_overide ? $request->persentase_overide/100 : 0.00,
                'nominal_overide' => $request->nominal_overide ? $request->nominal_overide : 0.00,
                'tanggal_selesai' => $request->masa_aktif ? null : $request->tanggal_selesai,
                'status' => "aktif",
            ]);
            SiswaDispensasi::updateOrCreate(['id'=>$request->id_siswa_dispensasi], $request->all());

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
