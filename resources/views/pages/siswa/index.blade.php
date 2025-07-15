@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Siswa')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Siswa')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Siswa">

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sukses! </strong>{{ session('message') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Peringatan! </strong>{{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="action mb-3">
        <a href="{{ route('siswa.form') }}" class="btn btn-primary">Tambah</a>
        <a href="{{ route('siswa.import.form') }}" class="btn btn-success">Import Excel</a>
        {{-- generate tagihan siswa --}}
        <a href="{{ route('tagihan.generate') }}" class="btn btn-warning">Generate Tagihan Siswa</a>
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
        function deleteData(id,nama){
            Swal.fire({
                title: "Data akan dihapus!",
                text: "Apakah Anda yakin data "+nama+" dihapus?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Tidak',
                confirmButtonColor: "#d33",
            }).then(function(result) {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "delete",
                        url: "/siswa/delete/"+id,
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                let timer = 1500
                                Swal.fire({
                                    title: "Sukses",
                                    text: response.message,
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: timer
                                });
                                setTimeout(() => {
                                    location.href = "{{ route('siswa.main') }}"
                                }, timer);
                            }else{
                                Swal.fire('Peringatan', response.message, 'warning')
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Terjadi Kesalahan', error, 'error')
                        }
                    });
                }
            });
        }

    </script>
@endpush
