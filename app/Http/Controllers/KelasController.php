<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Kelas;
use App\Helpers\LogPretty;
use App\Models\SIAKAD\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Kelas',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $kelas =  Kelas::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($kelas as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_kelas.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('kelas.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->nama_kelas,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.kelas.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Kelas::find($request->id) : [];
        $result = [];
        foreach (Jenjang::get()->toArray() as $item) {
            $result[$item['id']] = $item['nama_jenjang'];
        }
        $data['jenjang'] = $result;
        return view('pages.kelas.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_kelas' => 'required',
            'jenjang_id' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_kelas' => 'Nama Kelas',
            'jenjang_id' => 'Jenjang',
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
            Kelas::updateOrCreate(['id'=>$request->id_kelas],$request->all());

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
            $kelas = Kelas::findOrFail($id);
            $kelas->delete();

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
