@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Siswa Dispensasi')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Siswa Dispensasi')

{{-- Content body: main page content --}}

@section('content_body')

<x-adminlte-card title="Form Siswa Dispensasi">
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
    <form action="" method="post" id="formData">
        @csrf
        <input type="hidden" name="id_siswa_dispensasi" id="id_siswa_dispensasi" value="{{ $data ? old('id_siswa_dispensasi',$data->id) : old('id_siswa_dispensasi') }}">
        <input type="hidden" name="siswa_id" id="siswa_id" value="{{ $siswa->id ? old('siswa_id',$siswa->id) : old('siswa_id') }}">
        <div class="row">

            <x-adminlte-select name="pos_pemasukan_id" id="pos_pemasukan_id" label="Pos Pemasukan" fgroup-class="col-md-12">
                <x-adminlte-options :options="$pos_pemasukan"
                :selected="$data ? old('pos_pemasukan_id',$posPemasukanSelected) : old('pos_pemasukan_id')" empty-option=".:: Pilih Pos Pemasukan ::."/>
            </x-adminlte-select>

            <x-adminlte-select name="kategori_dispensasi_id" id="kategori_dispensasi_id" label="Kategori dispensasi" fgroup-class="col-md-6">
                <x-adminlte-options :options="$kategori_dispensasi"
                :selected="$data ? old('kategori_dispensasi_id',$kategoriDispensasiSelected) : old('kategori_dispensasi_id')"
                empty-option=".:: Pilih Kategori dispensasi ::."/>
            </x-adminlte-select>

            <div class="form-group col-md-6 mb-3 d-none">
                <label>Tipe</label>
                <div class="radio">
                    <input type="radio" name="tipe" id="tipeP" class="radio-button disabled" value="p" readonly required>
                    <label for="tipeP" style="font-weight: 300">Persentase</label>
                </div>
                <div class="radio">
                    <input type="radio" name="tipe" id="tipeN" class="radio-button disabled" value="n" readonly required>
                    <label for="tipeN" style="font-weight: 300">Nominal</label>
                </div>
            </div>

            <x-adminlte-input type="number" min="0" max="100" step="10" name="persentase_overide" label="Persentase" placeholder="Tulis Persentase" id="persentase_overide"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('persentase_overide',$data->persentase_overide*100) : old('persentase_overide') }}"/>

            <x-adminlte-input type="number" min="0" step="500" name="nominal_overide" label="Nominal" placeholder="Tulis Nominal" id="nominal_overide"
                fgroup-class="col-md-6" disable-feedback enable-old-support error-key value="{{ $data ? old('nominal_overide',number_format($data->nominal_overide,0,',','')) : old('nominal_overide') }}"/>
        </div>
        <div class="row">
            <x-adminlte-input type="date" name="tanggal_mulai" label="Tanggal Mulai" placeholder="Pilih Tanggal Mulai"
                fgroup-class="col-md-5" disable-feedback enable-old-support error-key value="{{ $data ? old('tanggal_mulai',$data->tanggal_mulai) : old('tanggal_mulai') }}"/>

            <div class="form-group col-md-2 d-flex align-items-center mb-0 pt-4">
                <label for="masa_aktif">
                    <input type="checkbox" name="masa_aktif" id="masa_aktif" {{ $data && $data->tanggal_selesai == null ? 'checked' : '' }} />
                    selamanya
                </label>
            </div>

            <x-adminlte-input type="date" name="tanggal_selesai" label="Tanggal Selesai" placeholder="Pilih Tanggal Selesai"
                fgroup-class="col-md-5" disable-feedback enable-old-support error-key value="{{ $data ? old('tanggal_selesai',$data->tanggal_selesai) : old('tanggal_selesai') }}"/>
        </div>
        <div class="row">
            <x-adminlte-textarea name="keterangan" label="Keterangan" placeholder="Tulis Keterangan" id="keterangan"
                fgroup-class="col-md-12" disable-feedback enable-old-support error-key>{{ $data ? old('keterangan',$data->keterangan) : old('keterangan') }}</x-adminlte-textarea>
        </div>
    </form>

    <a href="{{ $fromSiswa ? route('siswa_dispensasi.detail',['siswa_id'=>$siswa->id,'from'=>'siswa']) : route('siswa_dispensasi.detail',['siswa_id'=>$siswa->id]) }}" id="btnKembali" class="btn btn-default btn-flat">Kembali</a>

    <x-adminlte-button form="formData" class="btn-flat" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save" id="submitButton"/>
</x-adminlte-card>
@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
    <style>
        .radio-button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
            cursor: default; /* Fallback for older browsers */ readonly
        }
    </style>
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script>
        const dataSiswaDispensasi = {!! json_encode($data) !!};
        $(function () {
            $('#persentase_overide, #nominal_overide').parents('.form-group').hide()

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

            $("#kategori_dispensasi_id").change(function (e) {
                e.preventDefault();
                var selectedValue = $(this).val();
                getKategoriDispensasi(selectedValue);
            });

            $("#masa_aktif").change(function () {
                toggleMasaAktif();
            });

            // Inisialisasi kategori dispensasi jika data siswa dispensasi ada
            if(dataSiswaDispensasi){
                $("#kategori_dispensasi_id").val(dataSiswaDispensasi.kategori_dispensasi_id).trigger('change');
                toggleMasaAktif();
            }
        });
        function toggleTipe(param) {
            if (param === 'p') {
                $('#persentase_overide').parents('.form-group').show();
                $('#nominal_overide').parents('.form-group').hide();
                $("#nominal_overide").val('');
            } else if (param === 'n') {
                $('#nominal_overide').parents('.form-group').show();
                $('#persentase_overide').parents('.form-group').hide();
                $("#persentase_overide").val('');
            }else{
                $("#persentase_overide").val('');
                $("#nominal_overide").val('');
                $('#persentase_overide, #nominal_overide').parents('.form-group').hide();
            }
        }
        function getKategoriDispensasi(param) {
            $.ajax({
                url: "{{ route('referensi.kategori-dispensasi') }}",
                type: 'GET',
                data: { id: param },
                success: function (data) {
                    let nominal = parseFloat(data.nominal_default);
                    let persentase = parseFloat(data.persentase_default);

                    if(dataSiswaDispensasi && dataSiswaDispensasi.kategori_dispensasi_id == param){
                        console.log('menggunakan data siswa dispensasi');
                        // Jika data siswa dispensasi ada, gunakan nilai override
                        nominal = parseFloat(dataSiswaDispensasi.nominal_overide);
                        persentase = parseFloat(dataSiswaDispensasi.persentase_overide);
                    }

                    if(nominal.toFixed(2) > 0 && persentase.toFixed(2) == 0){
                        $("#tipeN").prop("checked", true).trigger("change");
                        $("#nominal_overide").val(parseInt(nominal));
                        $("#persentase_overide").val('');
                    }else if(persentase.toFixed(2) > 0 && nominal == 0){
                        $("#tipeP").prop("checked", true).trigger("change");
                        $("#persentase_overide").val(persentase.toFixed(2) * 100);
                        $("#nominal_overide").val('');
                    }else{
                        $("#tipeP").prop("checked",false).trigger("change");
                        $("#tipeN").prop("checked",false).trigger("change");
                        $("#persentase_overide").val('');
                        $("#nominal_overide").val('');
                        $('#persentase_overide, #nominal_overide').parents('.form-group').hide()
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire('Terjadi Kesalahan', error, 'error');
                }
            });
        }
        function toggleMasaAktif() {
            if ($('#masa_aktif').is(':checked')) {
                $('#tanggal_selesai').prop('disabled', true);
                $('#tanggal_selesai').val('');
            } else {
                $('#tanggal_selesai').prop('disabled', false);
                $('#tanggal_selesai').val('');
            }
        }
    </script>
    <script>
        $('#formData').submit(function (e) {
            e.preventDefault();
            let routeDetail = "/siswa-dispensasi/detail?siswa_id={{ $siswa->id }}{{ $fromSiswa ? '&from=siswa' : '' }}";
            const data = $('#formData').serialize()
            $.post("{{ route('siswa_dispensasi.store') }}", data)
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
                        location.href = routeDetail;
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
