<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Jenjang;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JenjangController extends Controller
{
    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Jenjang',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $jenjang =  Jenjang::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($jenjang as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_jenjang.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('jenjang.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->nama_jenjang,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.jenjang.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Jenjang::find($request->id) : [];
        return view('pages.jenjang.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_jenjang' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_jenjang' => 'Nama Jenjang',
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
            Jenjang::updateOrCreate(['id'=>$request->id_jenjang],$request->all());

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
            $jenjang = Jenjang::findOrFail($id);
            $jenjang->delete();

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
