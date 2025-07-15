@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Tahun Ajaran')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Tahun Ajaran')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Tahun Ajaran">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_tahun_ajaran" id="id_tahun_ajaran" value="{{ $data ? old('id_tahun_ajaran',$data->id) : old('id_tahun_ajaran') }}">
        <div class="row">
            <x-adminlte-input name="nama_tahun_ajaran" label="Nama Lengkap" placeholder="Tulis Nama Lengkap" id="nama_tahun_ajaran"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_tahun_ajaran',$data->nama_tahun_ajaran) : old('nama_tahun_ajaran') }}"/>
        </div>
        <div class="row">
            <x-adminlte-input type="date" name="tanggal_mulai" label="Tanggal Mulai"  placeholder="Tulis Tanggal Mulai"  id="tanggal_mulai"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('tanggal_mulai',$data->tanggal_mulai) : old('tanggal_mulai',date('Y-m-d')) }}"/>
            <x-adminlte-input type="date" name="tanggal_selesai" label="Tanggal Selesai"  placeholder="Tulis Tanggal Selesai"  id="tanggal_selesai"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('tanggal_selesai',$data->tanggal_selesai) : old('tanggal_selesai',date('Y-m-d')) }}"/>
        </div>
        <div class="row">
            <div class="form-group pl-2">
                <label for="is_aktif">Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="is_aktif" class="custom-control-input" id="is_aktif" @checked($is_aktif)>
                    <label class="custom-control-label" for="is_aktif">Aktif</label>
                </div>
            </div>
        </div>
    </form>

    <a href="{{ route('tahun_ajaran.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
            $.post("{{ route('tahun_ajaran.store') }}", data)
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
                        location.href = "{{ route('tahun_ajaran.main') }}"
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
