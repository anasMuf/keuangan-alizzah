@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pemasukan')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pemasukan')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Pemasukan">
    <div class="action mb-3">
        <a href="{{ route('pemasukan.form') }}" class="btn btn-primary">Tambah</a>
    </div>
    <div class="row filter mb-3">
        <div class="col-md-4">
            <form action="{{ route('pemasukan.main') }}" method="get" class="form-inline" id="filter">
                <div class="form-group">
                    <label for="angka_bulan" class="mr-2">Bulan:</label>
                    <select name="angka_bulan" id="angka_bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        @foreach($bulans as $b)
                            <option value="{{ $b->angka_bulan }}" {{ request('angka_bulan') == $b->angka_bulan || $bulanSekarang->angka_bulan == $b->angka_bulan ? 'selected' : '' }}>{{ $b->nama_bulan }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
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
        $('#angka_bulan').change(() => $('#filter').submit())
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
                        url: "/pemasukan/delete/"+id,
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
                                    location.href = "{{ route('pemasukan.main') }}"
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
