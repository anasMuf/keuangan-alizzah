<?php

namespace App\Http\Controllers;

use App\Models\Pos;
use App\Helpers\LogPretty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    public function index() {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Pos',
            'Tipe',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pos =  Pos::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($pos as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_pos.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('pos.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->nama_pos,
                $item->tipe,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pos.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Pos::find($request->id) : [];
        return view('pages.pos.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_pos' => 'required',
            'tipe' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_pos' => 'Nama Pos',
            'tipe' => 'Tipe Pos',
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
            Pos::updateOrCreate(['id'=>$request->id_pos],$request->all());

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
            $pos = Pos::findOrFail($id);
            $pos->delete();

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
