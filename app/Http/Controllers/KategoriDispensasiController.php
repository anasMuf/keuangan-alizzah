<?php

namespace App\Http\Controllers;

use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use App\Models\KategoriDispensasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KategoriDispensasiController extends Controller
{
    public function index() {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Kategori Dispensasi',
            'Keterangan',
            'Persentase',
            'Nominal',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $kategori_dispensasi =  KategoriDispensasi::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($kategori_dispensasi as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_kategori.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('kategori_dispensasi.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->nama_kategori,
                $item->keterangan,
                ($item->persentase_default*100).'%',
                'Rp '.number_format($item->nominal_default,0,',','.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.kategori_dispensasi.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? KategoriDispensasi::find($request->id) : [];

        return view('pages.kategori_dispensasi.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_kategori' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus format angka',
        ];
        $attributes = [
            'nama_kategori' => 'Nama Kategori Dispensasi',
            'keterangan' => 'Keterangan',
            'persentase_default' => 'Persentase',
            'nominal_default' => 'Nominal',
        ];
        $validator = Validator::make($request->all(),$rules,$messages,$attributes);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data, ',
                'message_validation' => $validator->getMessageBag()
            ]);
        }

        if($request->tipe === 'p'){
            $rules['persentase_default'] = 'required|numeric';
            $request->merge([
                'nominal_default' => 0,
                'persentase_default' => $request->persentase_default/100
            ]);
        }elseif($request->tipe === 'n'){
            $rules['nominal_default'] = 'required|numeric';
            $request->merge([
                'persentase_default' => 0
            ]);
        }

        DB::beginTransaction();
        try {
            KategoriDispensasi::updateOrCreate(['id'=>$request->id_kategori_dispensasi],$request->all());

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
            $kategori_dispensasi = KategoriDispensasi::findOrFail($id);
            $kategori_dispensasi->delete();

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
