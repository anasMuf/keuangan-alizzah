@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Tabungan Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Tabungan Siswa')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Tabungan Siswa">
    <div class="row details">
        <div class="col-md-6">
            <h5>Nama Siswa: {{ $siswaKelas->siswa->nama_lengkap }}</h5>
            <h5>Kelas: {{ $siswaKelas->kelas->nama_kelas }}</h5>
        </div>
        <div class="col-md-6 text-right">
            <h5>Total Tabungan: Rp {{ number_format($totalSaldo, 0,',','.') }}</h5>
        </div>
    </div>
    <hr>
    <div class="action mb-3">
        <a href="{{ route('tabungan_siswa.form',['siswa_id' => $siswaKelas->siswa->id, 'transaksi' => 'setor']) }}" class="btn btn-success">Setor</a>
        <a href="{{ route('tabungan_siswa.form',['siswa_id' => $siswaKelas->siswa->id, 'transaksi' => 'tarik']) }}" class="btn btn-danger">Tarik</a>
    </div>
    <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
        @foreach($config['data'] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
        @endforeach
    </x-adminlte-datatable>

    <a href="{{ route('tabungan_siswa.main') }}" class="btn btn-default btn-flat">Kembali</a>
</x-adminlte-card>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
    </script>
@endpush
