@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Siswa Kelas')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Siswa Kelas')
@section('plugins.DualListbox', true)

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Siswa Kelas">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_siswa_kelas" id="id_siswa_kelas" value="{{ $data ? old('id_siswa_kelas',$data->id) : old('id_siswa_kelas') }}">
        <div class="row">
            <x-adminlte-select name="siswa_id" label="Siswa" fgroup-class="col-md-6">
                <x-adminlte-options :options="$siswa"
                selected="{{ $data ? old('siswa_id',$data->siswa_id) : old('siswa_id') }}" empty-option=".:: Pilih Siswa ::."/>
            </x-adminlte-select>
            <x-adminlte-select name="kelas_id" label="Kelas" fgroup-class="col-md-6">
                <x-adminlte-options :options="$kelas"
                selected="{{ $data ? old('kelas_id',$data->kelas_id) : old('kelas_id') }}" empty-option=".:: Pilih Kelas ::."/>
            </x-adminlte-select>
        </div>
        <div class="row">
            <x-adminlte-select name="tahun_ajaran_id" label="Tahun Ajaran" fgroup-class="col-md-6">
                <x-adminlte-options :options="$tahun_ajaran"
                selected="{{ $data ? old('tahun_ajaran_id',$data->tahun_ajaran_id) : old('tahun_ajaran_id') }}" empty-option=".:: Pilih Tahun Ajaran ::."/>
            </x-adminlte-select>
            <x-adminlte-select name="status" label="Status" fgroup-class="col-md-6">
                <x-adminlte-options :options="$status"
                selected="{{ $data ? old('status',$data->status) : old('status') }}" empty-option=".:: Pilih Status ::."/>
            </x-adminlte-select>
        </div>
    </form>

    <a href="{{ route('siswa_kelas.main') }}" class="btn btn-default btn-flat">Kembali</a>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
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
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $('#formData').serialize()
            $.post("{{ route('siswa_kelas.store') }}", data)
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
                        location.href = "{{ route('siswa_kelas.main') }}"
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
