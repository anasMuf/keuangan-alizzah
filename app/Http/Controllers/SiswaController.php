<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Siswa;
use App\Helpers\LogPretty;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    public function index(){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Lengkap',
            'Kelas',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $siswa =  Siswa::with('siswa_kelas.kelas')
        ->whereHas('siswa_kelas', fn($q)=>$q->where('status', 'aktif'))
        ->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [null, null, null, ['orderable' => false]]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($siswa as $item){

            $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_lengkap.'`)">
                <i class="fa fa-lg fa-fw fa-trash"></i>
            </button>';
            $btnDetails = '<a href="'.route('siswa.form', ['id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->nama_lengkap,
                $item->siswa_kelas->first()->kelas->nama_kelas ?? 'Tidak ada kelas',
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.siswa.index', $data);
    }

    public function form(Request $request){
        $data['data'] = ($request->id) ? Siswa::find($request->id) : [];

        $data['jenisKelamin'] = ['Laki-laki'=>'Laki-laki','Perempuan'=>'Perempuan'];
        $jenisKelaminSelected = '';
        if($data['data']){
            $jenisKelaminSelected = $data['data']->jenis_kelamin;
        }
        $data['jenisKelaminSelected'] = $jenisKelaminSelected;

        return view('pages.siswa.form',$data);
    }

    public function store(Request $request){
        $rules = [
            'nama_lengkap' => 'required',
            'nama_panggilan' => 'required',
            'nik' => 'required',
        ];
        $messages = [
            'required' => ':attribute harus diisi',
        ];
        $attributes = [
            'nama_lengkap' => 'Nama Lengkap',
            'nama_panggilan' => 'Nama Panggilan',
            'nik' => 'No. KTP',
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
            Siswa::updateOrCreate(['id'=>$request->id_siswa],$request->all());

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
            $siswa = Siswa::findOrFail($id);
            $siswa->delete();

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


    public function importForm()
    {
        return view('pages.siswa.import');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:5120' // Increase to 5MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            $importErrors = $import->getImportErrors();
            $successMessage = 'Data siswa berhasil diimport!';

            if (!empty($importErrors)) {
                $successMessage .= ' Namun ada ' . count($importErrors) . ' baris yang dilewati karena error alamat.';
            }

            DB::commit();
            return redirect()->route('siswa.main')
                ->with('success', $successMessage)
                ->with('import_errors', $importErrors);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            Log::info("e: " .$e);
            LogPretty::error($e);
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            // return $errorMessages;
            return redirect()->back()
                ->withErrors(['import_errors' => $errorMessages])
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            LogPretty::error($e);
            // return $e;
            return redirect()->back()
                ->withErrors(['import_errors' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'jenjang',
            'asal_sekolah',
            'alamat_asal_sekolah',

            'nama_lengkap',
            'nama_panggilan',
            'nik',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',

            // Alamat columns
            'alamat_lengkap',
            'desa',
            'kecamatan',
            'kabupaten_kota',
            'provinsi',

            'agama',
            'kewarganegaraan',
            'anak_keberapa',
            'jumlah_saudara_kandung',
            'jumlah_saudara_tiri',
            'jumlah_saudara_angkat',
            'status_orangtua',
            'bahasa_seharihari',
            'golongan_darah',
            'riwayat_penyakit',
            'riwayat_imunisasi',
            'ciri_khusus',
            'cita_cita',

            'kelas'
        ];

        // Sample data untuk template
        $sampleData = [
            [
                'KB',
                'KB Sayang Ibu',
                'Banjarmasin',
                'Ahmad Budi Santoso',
                'Budi',
                '3273010101990001',
                'Laki-laki',
                'Jakarta',
                '1999-01-01',
                'Jl. Merdeka No. 123',
                'Menteng',
                'Menteng',
                'Jakarta Pusat',
                'DKI Jakarta',
                'Islam',
                'Indonesia',
                '1',
                '2',
                '0',
                '0',
                'Lengkap',
                'Indonesia',
                'O',
                'Tidak ada',
                'Lengkap',
                'Tinggi badan 150cm',
                'Dokter',
                'Intan 1'
            ]
        ];

        $filename = 'template_siswa.xlsx';

        return Excel::download(new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $headers;
            private $sampleData;

            public function __construct($headers, $sampleData) {
                $this->headers = $headers;
                $this->sampleData = $sampleData;
            }

            public function array(): array {
                return array_merge([$this->headers], $this->sampleData);
            }
        }, $filename);
    }
}
