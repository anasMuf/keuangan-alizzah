@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Siswa Dispensasi')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Siswa Dispensasi')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Data Siswa Dispensasi">
    <table style="width: 100%;">
        <tr>
            <td>Nama Siswa</td>
            <td>:</td>
            <td>{{ $siswa->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>
                @php
                    if ($siswa->siswa_kelas) {
                        foreach ($siswa->siswa_kelas as $value) {
                            if($value->status == 'aktif') {
                                echo $value->kelas->nama_kelas;
                            }
                        }
                    }
                @endphp
            </td>
        </tr>
    </table>
    <hr>
    <div class="action mb-3">
        <a href="{{ $fromSiswa ? route('siswa_dispensasi.form',['siswa_id'=>$siswa->id,'from'=>'siswa']) : route('siswa_dispensasi.form',['siswa_id'=>$siswa->id]) }}" class="btn btn-primary">Tambah</a>
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

    <a href="{{ $fromSiswa ? route('siswa.form',['id'=>$siswa->id]) : route('siswa_dispensasi.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
                        url: "/siswa_dispensasi/delete/"+id,
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
                                    location.href = "{{ route('siswa_dispensasi.main') }}"
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
