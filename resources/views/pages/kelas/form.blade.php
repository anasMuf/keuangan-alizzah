@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Kelas')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kelas')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Kelas">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_kelas" id="id_kelas" value="{{ $data ? old('id_kelas',$data->id) : old('id_kelas') }}">
        <div class="row">
            <x-adminlte-input name="nama_kelas" label="Nama Kelas" placeholder="Tulis Nama Kelas" id="nama_kelas"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_kelas',$data->nama_kelas) : old('nama_kelas') }}"/>
        </div>
        <div class="row">
            <x-adminlte-select name="jenjang_id" label="Jenjang" fgroup-class="col-md-6">
                <x-adminlte-options :options="$jenjang"
                selected="{{ $data ? old('jenjang_id',$data->jenjang_id) : old('jenjang_id') }}" empty-option=".:: Pilih Jenjang ::."/>
            </x-adminlte-select>
        </div>
    </form>

    <a href="{{ route('kelas.main') }}" class="btn btn-default btn-flat">Kembali</a>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
</x-adminlte-card>

@if($dataSiswa)
<x-adminlte-card title="Data Siswa" theme="light" class="mt-3">
    <a href="{{ route('siswa.form',['kelas_id' => $data->id]) }}" class="btn btn-primary btn-flat" id="tambahSiswaButton">Tambah Siswa</a>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dataSiswa as $siswa)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $siswa->siswa->nama_lengkap }}</td>
                        <td>
                            <button class="btn btn-xs btn-danger" onclick="deleteDataSiswaKelas({{ $siswa->id }}, '{{ $siswa->siswa->nama_lengkap }}')">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data siswa</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-adminlte-card>
@endif
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        function deleteDataSiswaKelas(id,nama){
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
                        url: "/siswa-kelas/delete/"+id,
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
                                    location.href = "/kelas/form?id="+$('#id_kelas').val()
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
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $('#formData').serialize()
            $.post("{{ route('kelas.store') }}", data)
            .done(function (response) {
                if(response.success){
                    let timer = 1500
                    Swal.fire({
                        title: "Sukses",
                        text: response.message,
                        icon: "success",
                        showConfirmButton: false,
                        timer: timer
                    })
                    setTimeout(() => {
                        location.href = "{{ route('kelas.main') }}"
                    }, timer);
                }else{
                    let message = response.message;
                        $.each(response.message_validation, function (i,msg) {
                            message += msg[0]+', <br>';
                        })
                    Swal.fire('Peringatan', message, 'warning')
                }
            })
            .fail(function(xhr,status,error){
                Swal.fire('Terjadi Kesalahan', error, 'error')
            });
        });
    </script>
@endpush
