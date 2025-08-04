<?php

namespace App\Http\Controllers;

use App\Models\SIAKAD\Siswa;
use Illuminate\Http\Request;
use App\Models\TabunganSiswa;
use App\Models\SIAKAD\SiswaKelas;
use App\Models\SIAKAD\TahunAjaran;

class TabunganSiswaController extends Controller
{
    public function index()
    {
        return "maintenance mode";
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Nama Siswa',
            'Saldo',
            ['label' => 'Actions', 'no-export' => true, 'width' => 5],
        ];

        $siswa = Siswa::with('tabungan_siswa')->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                ['orderable' => false]
            ]
        ];

        $btnDelete = '';
        $btnDetails = '';
        $no = 1;

        foreach($siswa as $item){
            if($item->tabungan_siswa->isEmpty()){
                continue;
            }
            // $btnDelete = '<button class="btn btn-danger btn-xs mx-1" title="Delete" id="btnDelete" onclick="deleteData('.$item->id.',`'.$item->nama_tabungan_siswa.'`)">
            //     <i class="fa fa-lg fa-fw fa-trash"></i>
            // </button>';
            $btnDetails = '<a href="'.route('tabungan_siswa.siswa', ['siswa_id' => $item->id]).'" class="btn btn-info btn-xs mx-1" title="Details">
                <i class="fa fa-lg fa-fw fa-eye"></i>
            </a>';

            $data['config']['data'][] = [
                $no++,
                $item->nama_lengkap,
                'Rp '.number_format($item->saldo_tabungan, 0, ',', '.'),
                '<nobr>'.$btnDelete.$btnDetails.'</nobr>'
            ];
        }

        return view('pages.tabungan_siswa.index', $data);
    }

    public function siswa($siswa_id){
        $data['heads'] = [
            ['label' => 'No', 'width' => 4],
            'Tanggal',
            'Keterangan',
            'Debit',
            'Kredit',
        ];

        $tabungan_siswa = TabunganSiswa::with('siswa')->where('siswa_id', $siswa_id)->get();

        $data['config'] = [
            'data' => [],
            'order' => [[0, 'asc']],
            'columns' => [
                null,
                null,
                null,
                null,
                null,
            ]
        ];

        $no = 1;

        foreach($tabungan_siswa as $item){
            if($item->tabungan_siswa->isEmpty()){
                continue;
            }

            $data['config']['data'][] = [
                $no++,
                $item->tanggal,
                $item->keterangan,
                'Rp '.number_format($item->debit, 0, ',', '.'),
                'Rp '.number_format($item->kredit, 0, ',', '.')
            ];
        }

        $data['siswaKelas'] = SiswaKelas::with('siswa','kelas')->where([
            'tahun_ajaran_id' => TahunAjaran::where('status', 'aktif')->first()->id,
            'siswa_id' => $siswa_id,
            'status' => 'aktif'
        ])->first();

        return view('pages.tabungan_siswa.siswa', $data);
    }
}
