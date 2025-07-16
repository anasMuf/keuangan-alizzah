<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Siswa;
use App\Helpers\LogPretty;
use App\Models\SIAKAD\Kelas;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SiswaKelasController extends Controller
{
    public function byKelas(Request $request){
        try {
            $data = SiswaKelas::with('siswa')->where([
                'kelas_id' => $request->kelas_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'status' => 'aktif',
            ])->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil ambil data',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil data, kesalahan pada sistem',
            ]);
        }
    }

    public function getSiswaTanpaKelas(Request $request){
        try {
            $tahunAjaran = TahunAjaran::where('is_aktif',true)->first();
            $data = Siswa::with('siswa_kelas')
                ->whereDoesntHave('siswa_kelas', function($query) use ($tahunAjaran) {
                    $query->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->where('status', 'aktif');
                })
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil ambil data',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            LogPretty::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil data, kesalahan pada sistem',
            ]);
        }
    }

    public function storeFromKelas(Request $request){
        $rules = [
            'nama_lengkap' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_lengkap' => 'Nama Lengkap',
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
            $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
            if(!$tahunAjaran){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data, tahun ajaran belum aktif',
                ]);
            }
            $siswa = Siswa::create([
                'nama_lengkap' => $request->nama_lengkap,
            ]);

            SiswaKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $request->kelas_id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'status' => 'aktif',
            ]);

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
            $siswaKelas = SiswaKelas::findOrFail($id);
            $siswaKelas->delete();

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
