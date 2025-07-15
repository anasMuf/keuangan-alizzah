@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Import Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Import Siswa')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Import Siswa">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="alert alert-warning">
            <h6>Error Alamat pada Import:</h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>
                        Siswa: {{ $error['row']['nama_lengkap'] ?? 'Tidak diketahui' }} -
                        {{ implode(', ', $error['errors']) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="text-center">
        <a href="{{ route('siswa.download.template') }}" class="btn btn-success">
            Download Template Excel
        </a>
    </div>
    <hr>
    <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Pilih File Excel</label>
            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
            <div class="form-text">Format yang didukung: .xlsx, .xls, .csv (maksimal 2MB)</div>
        </div>

        <div class="d-grid gap-2">
            <x-adminlte-button class="btn-flat" type="submit" label="Import Data" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
            <a href="{{ route('siswa.main') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>

</x-adminlte-card>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')

@endpush
