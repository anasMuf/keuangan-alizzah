<?php

namespace App\Http\Controllers;

use App\Models\Pos;
use App\Helpers\LogPretty;
use Illuminate\Support\Str;
use App\Models\PosPemasukan;
use Illuminate\Http\Request;
use App\Models\PosPengeluaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosPengeluaranController extends Controller
{
    public function index() {
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Pos',
            'Nama Pos Pengeluaran/Sub Pos',
            // 'Nominal',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $pos_pengeluaran =  PosPengeluaran::with('pos_pemasukan')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                // null,
                ['orderable' => false]
            ]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($pos_pengeluaran as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_pos_pengeluaran.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('pos_pengeluaran.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->pos_pemasukan->nama_pos_pemasukan,
                $item->nama_pos_pengeluaran,
                // 'Rp '.number_format($item->nominal_valid,0,',','.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.pos_pengeluaran.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? PosPengeluaran::find($request->id) : [];

        $resultPos = [];
        foreach (Pos::get()->toArray() as $item) {
            $resultPos[$item['id']] = $item['nama_pos'];
        }
        $data['pos'] = $resultPos;
        $data['kategori'] = [
            'beban_operasional' => Str::replace('_', ' ', Str::title('beban_operasional')),
            'beban_administrasi' => Str::replace('_', ' ', Str::title('beban_administrasi')),
        ];

        $resultPosPemasukan = [];
        foreach (PosPemasukan::get()->toArray() as $item) {
            $resultPosPemasukan[$item['id']] = $item['nama_pos_pemasukan'];
        }
        $data['pos_pemasukan'] = $resultPosPemasukan;
        $posPemasukanSelected = [];
        if($data['data']){
            $posPemasukanSelected[] = (string)$data['data']->pos_pemasukan_id;
        }
        $data['posPemasukanSelected'] = $posPemasukanSelected;
        return view('pages.pos_pengeluaran.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_pos_pengeluaran' => 'required',
            'kategori' => 'required',
            'nominal_valid' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_pos_pengeluaran' => 'Nama Pos Pengeluaran',
            'kategori' => 'Kategori',
            'nominal_valid' => 'Nominal',
        ];
        $request->merge([
            'pos_id' => 2
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
            PosPengeluaran::updateOrCreate(['id'=>$request->id_pos_pengeluaran],$request->all());

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
            $pos_pengeluaran = PosPengeluaran::findOrFail($id);
            $pos_pengeluaran->delete();

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
