@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Pos')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Pos')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Pos">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_pos" id="id_pos" value="{{ $data ? old('id_pos',$data->id) : old('id_pos') }}">
        <div class="row">
            <x-adminlte-input name="nama_pos" label="Nama Pos" placeholder="Tulis Nama Pos" id="nama_pos"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_pos',$data->nama_pos) : old('nama_pos') }}"/>

            <x-adminlte-select name="tipe" label="Tipe Pos" fgroup-class="col-md-6">
                <x-adminlte-options :options="['in'=>'in','out'=>'out']"
                selected="{{ old('tipe',$data->tipe) }}" empty-option=".:: Pilih Tipe Pos ::."/>
            </x-adminlte-select>
        </div>
    </form>

    <a href="{{ route('pos.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
            $.post("{{ route('pos.store') }}", data)
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
                        location.href = "{{ route('pos.main') }}"
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
