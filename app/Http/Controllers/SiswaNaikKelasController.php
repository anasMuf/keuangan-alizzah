<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Kelas;
use App\Models\SIAKAD\Siswa;
use App\Helpers\LogPretty;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use App\Models\PosPemasukan;
use App\Models\TagihanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TagihanSiswaService;
use Illuminate\Support\Facades\Validator;

class SiswaNaikKelasController extends Controller
{
    public function index() {
        $resultKelas = [];
        foreach (Kelas::get()->toArray() as $item) {
            $resultKelas[$item['id']] = $item['nama_kelas'];
        }
        $data['kelas'] = $resultKelas;
        $resultTahunAjaran = [];
        foreach (TahunAjaran::get()->toArray() as $item) {
            $resultTahunAjaran[$item['id']] = $item['nama_tahun_ajaran'];
        }
        $data['tahun_ajaran'] = $resultTahunAjaran;

        // $tahunAjaranAktif = TahunAjaran::whereDate('tanggal_mulai','<',date('Y-m-d'))->first() ?? TahunAjaran::orderBy('tanggal_mulai','asc')->first();
        $data['tahunAjaranAwal'] = '';//$tahunAjaranAktif->id;
        $data['tahunAjaranTujuan'] = '';//TahunAjaran::whereDate('tanggal_mulai','>',$tahunAjaranAktif->tanggal_selesai)->orderBy('tanggal_mulai','desc')->first()->id;
        $data['siswa'] = Siswa::get();

        return view('pages.siswa_naik_kelas.index',$data);
    }

    public function store(Request $request) {
        $rules = [
            'siswa_ids' => 'required|array',
            'kelas_tujuan' => 'required',
            'tahun_ajaran_tujuan' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
            'array' => ':attribute tidak sesuai format',
        ];
        $attributes = [
            'siswa_ids' => 'Pilih Siswa',
            'kelas_tujuan' => 'Kelas Tujuan',
            'tahun_ajaran_tujuan' => 'Tahun Ajaran',
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

            $tahunAjaran = TahunAjaran::find($request->tahun_ajaran_tujuan);
            if(!$tahunAjaran->is_aktif){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data, tahun ajaran belum aktif',
                    'data' => $tahunAjaran
                ]);
            }

            foreach($request->siswa_ids as $siswa_id){
                $siswa_kelas_lama = SiswaKelas::where('siswa_id',$siswa_id)->orderby('id','desc')->first();
                if($siswa_kelas_lama){
                    $siswa_kelas_lama->status = 'nonaktif';
                    $siswa_kelas_lama->save();
                }

                $siswa_kelas = new SiswaKelas;
                $siswa_kelas->siswa_id = $siswa_id;
                $siswa_kelas->kelas_id = $request->kelas_tujuan;
                $siswa_kelas->tahun_ajaran_id = $request->tahun_ajaran_tujuan;
                $siswa_kelas->status = 'aktif';
                $siswa_kelas->save();

                // buat tagihan siswa
                $tagihanSiswaService = new TagihanSiswaService();
                $dataTagihan = [
                    'tahun_ajaran_id' => $request->tahun_ajaran_tujuan,
                    'siswa_kelas' => SiswaKelas::with('kelas.jenjang','siswa.siswa_dispensasi')->find($siswa_kelas->id)->toArray(),
                    'siswa_dispensasi' => $siswa_kelas['siswa']['siswa_dispensasi'] ?? [],
                ];
                $resultTagihan = $tagihanSiswaService->create($dataTagihan);
                if(!$resultTagihan['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $resultTagihan['message'],
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
}
