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
}
