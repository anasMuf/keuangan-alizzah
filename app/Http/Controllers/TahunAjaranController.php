<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\LogPretty;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TahunAjaranController extends Controller
{
    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Tahun Ajaran',
            'Tanggal Mulai',
            'Tanggal Selesai',
            ['label' => 'Status Aktif', 'width' => 5],
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $tahun_ajaran =  TahunAjaran::get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, null, ['orderable' => false], ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $status = '';
        $no = 1;

        foreach($tahun_ajaran as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_tahun_ajaran.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('tahun_ajaran.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            if($item->is_aktif){
                $status = '<span class="badge badge-success">Aktif</span>';
            }else{
                $status = '<span class="badge badge-danger">Tidak Aktif</span>';
            }


            $tanggal_mulai = Carbon::parse($item->tanggal_mulai)->isoFormat('DD MMMM YYYY');
            $tanggal_selesai = Carbon::parse($item->tanggal_selesai)->isoFormat('DD MMMM YYYY');

            $data['config']['data'][] = [
                $no++,
                $item->nama_tahun_ajaran,
                $tanggal_mulai,
                $tanggal_selesai,
                $status,
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.tahun_ajaran.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? TahunAjaran::find($request->id) : [];
        $is_aktif = false;
        if($request->id && $data['data']->is_aktif){
            $is_aktif = true;
        }elseif($request->id && !$data['data']->is_aktif){
            $is_aktif = false;
        }
        $data['is_aktif'] = $is_aktif;
        return view('pages.tahun_ajaran.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_tahun_ajaran' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'is_aktif' => 'required|boolean',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'date' => ':attribute harus berupa format tanggal',
            'boolean' => ':attribute harus dipilih ya/tidak',
        ];
        $attributes = [
            'nama_tahun_ajaran' => 'Nama Tahun Ajaran',
            'tanggal_mulai' => 'Tanggal Mulai',
            'tanggal_selesai' => 'Tanggal Mulai',
            'is_aktif' => 'Status',
        ];

        $request->merge([
            'is_aktif' => isset($request->is_aktif) ? true : false
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
            TahunAjaran::updateOrCreate(['id'=>$request->id_tahun_ajaran],$request->all());

            if($request->is_aktif === true){
                TahunAjaran::where('id','!=',$request->id_tahun_ajaran)->update([
                    'is_aktif' => false
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

    public function delete($id){
        DB::beginTransaction();
        try {
            $tahun_ajaran = TahunAjaran::findOrFail($id);
            $tahun_ajaran->delete();

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
