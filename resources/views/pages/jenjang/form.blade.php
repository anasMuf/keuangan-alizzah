@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Jenjang')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Jenjang')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Jenjang">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_jenjang" id="id_jenjang" value="{{ $data ? old('id_jenjang',$data->id) : old('id_jenjang') }}">
        <div class="row">
            <x-adminlte-input name="nama_jenjang" label="Nama Jenjang" placeholder="Tulis Nama Jenjang" id="nama_jenjang"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_jenjang',$data->nama_jenjang) : old('nama_jenjang') }}"/>
        </div>
    </form>

    <a href="{{ route('jenjang.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
            $.post("{{ route('jenjang.store') }}", data)
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
                        location.href = "{{ route('jenjang.main') }}"
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
