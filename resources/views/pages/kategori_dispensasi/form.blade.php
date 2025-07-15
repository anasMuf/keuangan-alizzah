@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Kategori Dispensasi')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kategori Dispensasi')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Kategori Dispensasi">
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_kategori_dispensasi" id="id_kategori_dispensasi" value="{{ $data ? old('id_kategori_dispensasi',$data->id) : old('id_kategori_dispensasi') }}">
        <div class="row">
            <x-adminlte-input name="nama_kategori" label="Nama Kategori Dispensasi" placeholder="Tulis Nama Kategori Dispensasi" id="nama_kategori"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nama_kategori',$data->nama_kategori) : old('nama_kategori') }}"/>

            <x-adminlte-textarea name="keterangan" label="Keterangan" placeholder="Tulis Keterangan" id="keterangan"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key>{{ $data ? old('keterangan',$data->keterangan) : old('keterangan') }}</x-adminlte-textarea>

            <div class="col-md-6 mb-3">
                <label>Tipe</label>
                <div class="radio">
                    <input type="radio" name="tipe" id="tipeP" class="radio-button" value="p" {{ $data && $data->persentase_default > 0 ? 'checked' : '' }} required>
                    <label for="tipeP" style="font-weight: 300">Persentase</label>
                </div>
                <div class="radio">
                    <input type="radio" name="tipe" id="tipeN" class="radio-button" value="n" {{ $data && $data->nominal_default > 0 ? 'checked' : '' }} required>
                    <label for="tipeN" style="font-weight: 300">Nominal</label>
                </div>
            </div>

            <x-adminlte-input type="number" min="0" max="100" step="10" name="persentase_default" label="Persentase" placeholder="Tulis Persentase" id="persentase_default"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('persentase_default',$data->persentase_default*100) : old('persentase_default') }}"/>

            <x-adminlte-input type="number" min="0" step="500" name="nominal_default" label="Nominal" placeholder="Tulis Nominal" id="nominal_default"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nominal_default',number_format($data->nominal_default,0,',','')) : old('nominal_default') }}"/>
        </div>
    </form>

    <a href="{{ route('kategori_dispensasi.main') }}" class="btn btn-default btn-flat">Kembali</a>

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
        $(function () {
            $('#persentase_default, #nominal_default').parents('.form-group').hide()

            $('.radio-button').each(function () {
                var checkedValue = $(this).is(':checked');
                if(checkedValue){
                    toggleTipe($(this).val())
                }
            })
            $('input[name="tipe"]').change(function() {
                var selectedValue = $(this).val();
                toggleTipe(selectedValue)
            });
        });
        function toggleTipe(param) {
            if (param === 'p') {
                $('#persentase_default').parents('.form-group').show();
                $('#nominal_default').parents('.form-group').hide();
                $("#nominal_default").val('');
            } else if (param === 'n') {
                $('#nominal_default').parents('.form-group').show();
                $('#persentase_default').parents('.form-group').hide();
                $("#persentase_default").val('');
            }else{
                $("#persentase_default").val('');
                $("#nominal_default").val('');
                $('#persentase_default, #nominal_default').parents('.form-group').hide();
            }
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            const data = $('#formData').serialize()
            $.post("{{ route('kategori_dispensasi.store') }}", data)
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
                        location.href = "{{ route('kategori_dispensasi.main') }}"
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
